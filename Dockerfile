FROM php:8.2-apache

# Paquetes útiles + mysql client
RUN apt-get update \
  && apt-get install -y --no-install-recommends zip unzip default-mysql-client \
  && rm -rf /var/lib/apt/lists/*

# PDO MySQL
RUN docker-php-ext-install pdo_mysql \
  && docker-php-ext-enable pdo_mysql

# ✅ FIX MPM: dejar solo prefork activo
RUN a2dismod mpm_event 2>/dev/null || true \
  && a2dismod mpm_worker 2>/dev/null || true \
  && a2enmod mpm_prefork

# Rewrite y AllowOverride para .htaccess
RUN a2enmod rewrite \
  && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar app
COPY src/ /var/www/html/

# Instalar dependencias (Twig)
RUN composer install --no-dev --optimize-autoloader || composer install --optimize-autoloader

# Seed + entrypoint
COPY docker/init/hotel.sql /app/hotel.sql
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
