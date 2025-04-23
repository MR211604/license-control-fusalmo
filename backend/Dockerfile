FROM php:8.2-cli

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    nodejs \
    npm

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql zip

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar nodemon globalmente
RUN npm install -g nodemon

# Establecer directorio de trabajo
WORKDIR /var/www/backend

# Copiar archivos de la aplicación
COPY . .

# Instalar dependencias de PHP
RUN composer install

# Exponer el puerto en el que corre la aplicación
EXPOSE 8080

# Comando para iniciar el servidor
CMD ["nodemon", "server.php"]