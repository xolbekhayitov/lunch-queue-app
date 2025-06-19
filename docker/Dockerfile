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
    cron \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# RUN chmod -R 777 /var/www/storage/logs


# COPY docker/laravel-cron /etc/cron.d/laravel-cron
# RUN chmod 0644 /etc/cron.d/laravel-cron
# RUN crontab /etc/cron.d/laravel-cron
# Supervisor config
# COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN touch /var/log/cron.log

# Cronni va php-fpmni birga ishga tushurish uchun supervisord o‘rniga bash ishlatamiz
# If the cron functionality is needed, ensure the laravel-cron file exists and provide the correct path.
CMD ["sh", "-c", "cron && php-fpm"]
