FROM php:8.2-apache

# Install mysqli and enable it
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

WORKDIR /var/www/html
COPY app/ .

EXPOSE 80
