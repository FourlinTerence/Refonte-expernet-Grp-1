# Start your image with a node base image
FROM node:22-alpine


WORKDIR C:/Users/ltotobe.cda10/refonte-expernet-grp1/Refonte-expernet-Grp-1

# pas encore de fichier package.json donc pas besoin de cette ligne de code
#COPY package*.json ./

# Copie les dossiers locaux vers les dossier encours d-utilisation de l-image docker  (/app)
COPY ..

#COPY .npmrc .
#RUN npm ci \
#    && npm install -g serve@latest \
#    && npm run build \
#    && rm -fr node_modules

EXPOSE 3000

# Start the app using serve command
CMD [ "serve", "-s", "build" ]