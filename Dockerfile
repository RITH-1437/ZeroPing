FROM php:8.3-fpm

ARG user=zeroping
ARG uid=1000

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY . .

RUN echo "===== ROOT =====" && ls -la

RUN echo "===== FRAMEWORK SITE =====" && \
    ls -la framework-site || echo "framework-site NOT FOUND"

RUN useradd -G www-data,root -u $uid -d /home/$user $user && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --prefer-dist --no-interaction --optimize-autoloader

RUN chown -R $user:$user /var/www

USER $user

EXPOSE 1437

CMD sh -c "php -S 0.0.0.0:${PORT:-1437} -t public"