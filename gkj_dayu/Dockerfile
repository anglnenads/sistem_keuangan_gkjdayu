
FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip mysqli

# Enable Apache's mod_rewrite module.
RUN a2enmod rewrite

#Set the working directory for all subsequent commands.

WORKDIR /var/www/html


COPY . .

RUN chown -R www-data:www-data /var/www/html
