FROM php:8.4-fpm
WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    curl \
    git \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# FROM php:8.4-fpm

# WORKDIR /var/www

# # Laravel va PostgreSQL uchun kerakli paketlar
# RUN apt-get update && apt-get install -y \
#     libpng-dev \
#     libjpeg-dev \
#     libonig-dev \
#     libxml2-dev \
#     libzip-dev \
#     curl \
#     git \
#     libpq-dev \
#     supervisor \
#     && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# # Composer o'rnatish
# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# # Supervisor konfiguratsiyasini nusxalash
# COPY supervisord.conf /etc/supervisord.conf

# # Laravel artisan queue va nutgram bot doimiy ishlasin
# CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
