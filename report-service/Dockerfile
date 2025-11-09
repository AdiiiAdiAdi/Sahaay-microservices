FROM php:8.2-apache
WORKDIR /var/www/html
COPY app/ .
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Create uploads directory and give permission
RUN mkdir -p /var/www/html/uploads && chmod -R 777 /var/www/html/uploads

EXPOSE 80
