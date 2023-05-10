FROM php:8.2-rc-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    build-essential \
    gifsicle \
    git \
    jpegoptim \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libsodium-dev \
    libmagickwand-dev \
    libwebp-dev \
    libxpm-dev \
    libzip-dev \
    libonig-dev \
    locales \
    optipng \
    pngquant \
    unzip \
    zip \
    --no-install-recommends \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


RUN docker-php-ext-install pdo pdo_mysql bcmath sodium mbstring zip exif pcntl gd

RUN docker-php-ext-configure gd \
    --enable-gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    --with-xpm


RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

COPY package*.json ./

COPY composer.* ./

RUN composer install --prefer-dist --no-scripts && rm -rf /root/.composer

ENV TZ Asia/Riyadh
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
COPY php.ini $PHP_INI_DIR/php.ini
COPY .env.example ./.env

COPY fpm/zz-disable-access-logs.conf /usr/local/etc/php-fpm.d/zz-disable-access-logs.conf

COPY . .

RUN chown -R www-data:www-data /var/www/

RUN php artisan key:gen
RUN composer dump-autoload --no-scripts --optimize
