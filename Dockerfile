FROM php:8.1-apache

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    curl \
    git

RUN docker-php-ext-install pdo pdo_mysql zip

WORKDIR /var/www/html

# Copy configuration
COPY . /var/www/html

COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Run Command
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod +x /var/www/html/init.sh

EXPOSE 80

CMD ["apache2-foreground"]

