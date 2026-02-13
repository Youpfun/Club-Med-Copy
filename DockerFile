FROM php:8.2-apache

# 1. Installer les outils nécessaires (Zip, Git, Node.js pour le CSS/JS)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm

# 2. Installer les extensions PHP (pour la base de données)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Configurer Apache pour qu'il pointe vers le dossier /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# 4. Installer Composer (gestionnaire PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Copier ton code dans le conteneur
WORKDIR /var/www/html
COPY . .

# 6. Installer les dépendances et construire le site
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# 7. Donner les permissions à Apache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache