FROM php:8.2-fpm

# PHP 拡張インストール
RUN apt-get update && apt-get install -y \
        libzip-dev \
        libsqlite3-dev \
        unzip \
        git \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_sqlite \
        zip \
        bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer インストール
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# エントリポイントスクリプト
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
