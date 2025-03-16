FROM php:8.1-apache

# Set the working directory in the container
WORKDIR /var/www/html

# Copy the application code into the container
COPY . .
# Install the mysqli extension
RUN docker-php-ext-install mysqli
# Expose port 80 for the web server
EXPOSE 80

# Optional: Install any needed extensions
# RUN docker-php-ext-install mysqli pdo pdo_mysql