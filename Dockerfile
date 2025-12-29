FROM php:8.2-apache

# Paquetes útiles + mysql client (para seed)
RUN apt-get update \
  && apt-get install -y --no-install-recommends zip unzip default-mysql-client \
  && rm -rf /var/lib/apt/lists/*

# PDO MySQL
RUN docker-php-ext-install pdo_mysql \
  && docker-php-ext-enable pdo_mysql

# ✅ FIX MPM definitivo: borrar cualquier MPM activo y dejar SOLO prefork
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf \
         /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf \
         /etc/apache2/mods-enabled/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.conf \
  && a2enmod mpm_prefork

# Rewrite y AllowOverride para .htaccess
RUN a2enmod rewrite \
  && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar app al contenedor (en local lo sobrescribe el volume del compose)
COPY src/ /var/www/html/

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader || composer install --optimize-autoloader

# Seed + entrypoint
COPY docker/init/hotel.sql /app/hotel.sql
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
