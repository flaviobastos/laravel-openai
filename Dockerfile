# Usa a imagem PHP 8.3.9 FPM com Alpine 3.20 como base
FROM php:8.3.9-fpm-alpine3.20

# Define o diretório de trabalho
WORKDIR /var/www/html

# Instala pacotes necessários e dependências PHP
RUN apk add --no-cache \
    bash \
    git \
    curl \
    wget \
    nano \
    openssl \
    nodejs \
    npm \
    mysql-client && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    docker-php-ext-install pdo pdo_mysql mysqli

# Copiar os arquivos da aplicação para o diretório de trabalho
COPY . /var/www/html