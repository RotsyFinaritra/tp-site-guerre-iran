FROM php:8.2-apache

# Installer extensions PHP nécessaires
# - pdo/pdo_mysql: accès DB
# - gd: compression/redimensionnement images (TinyMCE upload)
RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
		libwebp-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
	&& docker-php-ext-install -j$(nproc) pdo pdo_mysql gd \
	&& rm -rf /var/lib/apt/lists/*

# Activer des modules Apache utiles
# - rewrite: routing
# - deflate: gzip pour HTML/CSS/JS
# - headers/expires: cache navigateur
RUN a2enmod rewrite deflate headers expires

# Copier configuration Apache
COPY apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Définir le dossier de travail
WORKDIR /var/www/html

# Permissions (optionnel mais utile)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80