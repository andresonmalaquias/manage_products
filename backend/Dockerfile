# Base do PHP com FPM
FROM php:8.2-fpm

# Instalar o Nginx e outras dependências necessárias
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    curl \
    unzip \
    libicu-dev \
    libxml2-dev \
    libzip-dev \
    libssl-dev \
    libonig-dev \
    libsodium-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo mysqli pdo_mysql \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install fileinfo \
    && docker-php-ext-install intl \
    && docker-php-ext-install opcache

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar o código do Laravel para o container
COPY . /var/www/

# Definir o diretório de trabalho
WORKDIR /var/www

# Instalar dependências do Composer
RUN composer install --no-dev --optimize-autoloader

# Configurar permissões
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copiar o arquivo de configuração do Nginx
COPY ./nginx.conf /etc/nginx/nginx.conf

# Expor a porta 80
EXPOSE 80

# Comando para iniciar Nginx e PHP-FPM
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
