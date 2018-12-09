FROM php:7.2-fpm-alpine AS production-image

WORKDIR /var/www/app

RUN apk --update add \
    curl \
    git \
    bash \
    build-base \
    libmemcached-dev \
    libmcrypt-dev \
    libxml2-dev \
    zlib-dev \
    autoconf \
    cyrus-sasl-dev \
    libgsasl-dev \
    # Install extensions
    && docker-php-ext-install \
        opcache \
        bcmath \
        mbstring \
        pdo \
        tokenizer \
        xml \
        pcntl \
    && apk --update add postgresql-dev \
        && docker-php-ext-install pgsql pdo_pgsql \
    && apk --update add libzip-dev \
        && docker-php-ext-configure zip --with-libzip \
        && docker-php-ext-install zip \
    && pecl channel-update pecl.php.net \
        && pecl install mcrypt-1.0.1 \
    # Install composer
    && curl https://getcomposer.org/composer.phar -o /bin/composer \
        && chmod +x /bin/composer

# Setup FPM
ADD .docker/php/usr/local/etc/php/conf.d/app.ini /usr/local/etc/php/conf.d/app.ini
ADD .docker/php/usr/local/etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf

# package app, see .dockerignore
ADD . .

# Install app composer dependencies
RUN composer install --optimize-autoloader --no-dev --classmap-authoritative

CMD ["php-fpm", "-F"]

FROM production-image AS development-image

RUN curl https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh -o /bin/wait-for-it \
    # Install wait-for-it
        && chmod +x /bin/wait-for-it \
    # Install the xdebug extension
    && pecl install xdebug \
        && docker-php-ext-enable xdebug
ADD .docker/php/usr/local/etc/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
