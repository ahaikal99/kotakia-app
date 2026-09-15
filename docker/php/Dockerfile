FROM php:8.3-fpm

# Pasang dependencies sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Bersihkan cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Pasang PHP extensions (Penting untuk MariaDB/MySQL)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Pasang Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Pasang Node.js & NPM (Untuk Tailwind CSS & Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Tetapkan direktori kerja
WORKDIR /var/www/html