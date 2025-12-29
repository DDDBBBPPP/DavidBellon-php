FROM php:8.2-apache

# Paquetes útiles: zip/unzip (composer) + mysql client (para importar hotel.sql en Railway si hace falta)
RUN apt-get update \
  && apt-get install -y --no-install-recommends zip unzip default-mysql-client \
  && rm -rf /var/lib/apt/lists/*

# PDO MySQL
RUN docker-php-ext-install pdo_mysql \
  && docker-php-ext-enable pdo_mysql

# Reescritura y .htaccess
RUN a2enmod rewrite \
  && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiamos código (en local docker-compose lo montará por volumen igualmente, así que no rompe)
COPY src/ /var/www/html/

# Dependencias PHP (Twig)
RUN composer install --no-dev --optimize-autoloader || composer install --optimize-autoloader

# SQL de seed + entrypoint
COPY docker/init/hotel.sql /app/hotel.sql
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
