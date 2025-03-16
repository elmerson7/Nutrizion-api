
# Usa la imagen base de PHP 8.2 CLI
FROM php:8.2-cli

# Instala dependencias necesarias del sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        zip \
        libonig-dev \
        libxml2-dev \
        libssl-dev \
    && docker-php-ext-install mbstring xml zip

RUN pecl install grpc
RUN pecl install protobuf
RUN docker-php-ext-enable grpc protobuf
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilita extensiones de PHP necesarias
RUN docker-php-ext-install pcntl \
    && docker-php-ext-enable mbstring

# Instala Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copia tu archivo php.ini personalizado
# COPY docker/php.ini /usr/local/etc/php/php.ini

# Define el directorio de trabajo
WORKDIR /var/www/html

# Copia el código fuente del proyecto
COPY . /var/www/html

# Instala dependencias con Composer
RUN composer install --no-dev --optimize-autoloader

# Expone el puerto 8000
EXPOSE 8000

# Comando para iniciar la API
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
