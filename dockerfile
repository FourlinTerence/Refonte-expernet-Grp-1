# Use the official Nginx image as the base image
FROM nginx:alpine

# Copy the HTML website files to the Nginx directory
COPY ./ /usr/share/nginx/html/

# Expose port 80 to allow external access
EXPOSE 80

# fait fonctionner Nginx 
CMD ["nginx", "-g", "daemon off;"]