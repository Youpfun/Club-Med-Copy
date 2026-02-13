FROM php:8.2-apache

# 1. Installer les outils nécessaires
# J'ai ajouté 'libpq-dev' ici, c'est indispensable pour PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm

# 2. Installer les extensions PHP
# J'ai ajouté 'pdo_pgsql' ici pour que Laravel puisse parler à ta base de données
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# 3. Configurer Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# 4. Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Copier ton code
WORKDIR /var/www/html
COPY . .

# 6. Installer les dépendances
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# 7. Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache