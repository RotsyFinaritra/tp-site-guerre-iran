FROM php:8.2-apache

# Installer extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite (IMPORTANT pour SEO)
RUN a2enmod rewrite

# Copier configuration Apache
COPY apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Définir le dossier de travail
WORKDIR /var/www/html

# Permissions (optionnel mais utile)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80