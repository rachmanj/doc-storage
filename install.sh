#!/bin/bash

# Document Storage API Installation Script
# This script helps set up the Document Storage API with PHP 8.1

echo "Document Storage API Installation"
echo "================================="
echo

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "Detected PHP version: $PHP_VERSION"

if [[ "$PHP_VERSION" < "8.1" ]]; then
    echo "Error: PHP 8.1 or higher is required."
    echo "Please upgrade your PHP version and try again."
    exit 1
fi

# Install dependencies
echo "Installing dependencies..."
composer install

# Create environment file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating environment file..."
    cp .env.example .env
    
    # Generate application key
    echo "Generating application key..."
    php artisan key:generate
fi

# Create database
echo "Setting up database..."
touch database/database.sqlite

# Run migrations
echo "Running migrations..."
php artisan migrate

# Create storage link
echo "Creating storage link..."
php artisan storage:link

# Set permissions
echo "Setting file permissions..."
chmod -R 775 storage bootstrap/cache

echo
echo "Installation completed successfully!"
echo
echo "To start the development server, run:"
echo "php artisan serve"
echo
echo "Don't forget to set your API key in the .env file:"
echo "INVOICE_APP_API_KEY=your_secure_api_key_here"
echo 