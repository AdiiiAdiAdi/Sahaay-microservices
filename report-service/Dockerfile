FROM php:8.2-apache

WORKDIR /var/www/html

# Install dependencies (git, unzip, composer)
RUN apt-get update && apt-get install -y git unzip && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Copy app files
COPY app/ .

# Install AWS SDK for PHP
RUN composer require aws/aws-sdk-php

# Enable PHP extensions
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Create uploads directory
RUN mkdir -p /var/www/html/uploads && chmod -R 777 /var/www/html/uploads

EXPOSE 80
