FROM dysnomia/net-sdk-6-0 AS build-env
WORKDIR /app

ARG SONAR_HOST
ARG SONAR_TOKEN
ARG DEHASHME_CONNECTIONSTRING
ARG DRONE_BRANCH

# Build Project
COPY . ./

RUN jq ".AppSettings.ConnectionString = \"$DEHASHME_CONNECTIONSTRING\"" Dysnomia.DehashMe.WebApp/appsettings.json > tmp.appsettings.json && mv tmp.appsettings.json Dysnomia.DehashMe.WebApp/appsettings.json

RUN dotnet sonarscanner begin /k:"dehash-me" /d:sonar.host.url="$SONAR_HOST" /d:sonar.login="$SONAR_TOKEN" /d:sonar.cs.opencover.reportsPaths="**/coverage.opencover.xml" /d:sonar.coverage.exclusions="**Test*.cs" /d:sonar.branch.name="$DRONE_BRANCH"
RUN dotnet restore Dysnomia.DehashMe.sln --ignore-failed-sources /p:EnableDefaultItems=false
RUN dotnet publish Dysnomia.DehashMe.sln --no-restore -c Release -o out
RUN dotnet test /p:CollectCoverage=true /p:CoverletOutputFormat=opencover
RUN dotnet sonarscanner end /d:sonar.login="$SONAR_TOKEN"

# Build runtime image
FROM dysnomia/net-runtime-6-0
WORKDIR /app
COPY --from=build-env /app/out .
HEALTHCHECK --interval=2m --timeout=3s CMD curl -f http://localhost/ && curl -f http://localhost/count || exit 1
ENTRYPOINT ["dotnet", "Dysnomia.DehashMe.WebApp.dll"]