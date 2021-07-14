FROM dysnomia/net-sdk-5-0 AS build-env
WORKDIR /app

ARG SONAR_HOST
ARG SONAR_TOKEN
ARG DEHASHME_CONNECTIONSTRING

# Build Project
COPY . ./

RUN jq ".AppSettings.ConnectionString = \"$DEHASHME_CONNECTIONSTRING\"" Dysnomia.DehashMe.WebApp/appsettings.json > tmp.appsettings.json && mv tmp.appsettings.json Dysnomia.DehashMe.WebApp/appsettings.json
RUN jq ".AppSettings.ConnectionString = \"$DEHASHME_CONNECTIONSTRING\"" Dysnomia.DehashMe.WebApp.Tests/appsettings.json > tmp.appsettings.json && mv tmp.appsettings.json Dysnomia.DehashMe.WebApp.Tests/appsettings.json

RUN dotnet sonarscanner begin /k:"dehash-me" /d:sonar.host.url="$SONAR_HOST" /d:sonar.login="$SONAR_TOKEN" /d:sonar.cs.opencover.reportsPaths="**/coverage.opencover.xml" /d:sonar.coverage.exclusions="**Test*.cs"
RUN dotnet restore Dysnomia.DehashMe.sln --ignore-failed-sources /p:EnableDefaultItems=false
RUN dotnet publish Dysnomia.DehashMe.sln --no-restore -c Release -o out
RUN dotnet test /p:CollectCoverage=true /p:CoverletOutputFormat=opencover
RUN dotnet sonarscanner end /d:sonar.login="$SONAR_TOKEN"

# Build runtime image
FROM dysnomia/net-runtime-5-0
WORKDIR /app
COPY --from=build-env /app/out .
HEALTHCHECK --interval=5m --timeout=3s CMD curl -f http://localhost/ && curl -f http://localhost/count || exit 1
ENTRYPOINT ["dotnet", "Dysnomia.DehashMe.WebApp.dll"]