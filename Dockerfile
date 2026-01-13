FROM php:8.1-cli

# Install system dependencies (git, unzip for Composer)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json .

RUN composer install

COPY . .

CMD ["bash"]