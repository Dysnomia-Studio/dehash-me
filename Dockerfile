FROM node:12
WORKDIR /app

# Build Project
COPY . /app/
RUN npm install

ENTRYPOINT ["npm", "start"]