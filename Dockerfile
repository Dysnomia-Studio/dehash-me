FROM node:16
WORKDIR /app

# Build Project
COPY . /app/
RUN npm install

ENTRYPOINT ["npm", "start"]