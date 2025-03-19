FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    gnupg \
    g++ \
    procps \
    openssl \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg-dev \
    libicu-dev  \
    libonig-dev \
    libxslt1-dev \
    acl

RUN docker-php-ext-configure zip

RUN docker-php-ext-install \
    pdo pdo_mysql zip xsl gd intl opcache exif mbstring

# Alias
RUN echo 'alias sf="php bin/console"' >> ~/.bashrc

WORKDIR /var/www/symfony

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
# Проверка наличия файла Symfony и перемещение его в /usr/local/bin
RUN if [ -f /root/.symfony*/bin/symfony ]; then \
        mv /root/.symfony*/bin/symfony /usr/local/bin/symfony; \
    else \
        echo "Symfony CLI не установлен"; \
        exit 1; \
    fi