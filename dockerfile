# Use an official PHP runtime as a parent image
FROM php:7.4-fpm

# Set the working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application code
COPY . /app

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /app
# Add a non-root user
RUN addgroup --system appgroup && adduser --system --ingroup appgroup appuser

# Switch to the non-root user
USER appuser

# Run Composer as the non-root user
RUN composer install --ignore-platform-reqs

# Expose port 9000 and start php-fpm server
EXPOSE 8080
CMD ["php-fpm"]