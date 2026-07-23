FROM php:8.3-cli

# Install system dependencies & PHP extensions required for Laravel & MySQL
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql zip

WORKDIR /var/www/html

# Expose port 8080 for Laravel serve
EXPOSE 8080

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
