FROM php:8.2-apache

RUN apt-get update \
  && apt-get install -y --no-install-recommends zip unzip default-mysql-client \
  && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql \
  && docker-php-ext-enable pdo_mysql

RUN a2enmod rewrite \
  && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY src/ /var/www/html/

RUN composer install --no-dev --optimize-autoloader || composer install --optimize-autoloader

COPY docker/init/hotel.sql /app/hotel.sql
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
