@echo off
echo Document Storage API Installation
echo =================================
echo.

REM Check PHP version
for /f "tokens=*" %%a in ('php -r "echo PHP_VERSION;"') do set PHP_VERSION=%%a
echo Detected PHP version: %PHP_VERSION%

REM Install dependencies
echo Installing dependencies...
call composer install

REM Create environment file if it doesn't exist
if not exist .env (
    echo Creating environment file...
    copy .env.example .env
    
    REM Generate application key
    echo Generating application key...
    php artisan key:generate
)

REM Create database directory if it doesn't exist
if not exist database (
    mkdir database
)

REM Create database
echo Setting up database...
type nul > database\database.sqlite

REM Run migrations
echo Running migrations...
php artisan migrate

REM Create storage link
echo Creating storage link...
php artisan storage:link

echo.
echo Installation completed successfully!
echo.
echo To start the development server, run:
echo php artisan serve
echo.
echo Don't forget to set your API key in the .env file:
echo INVOICE_APP_API_KEY=your_secure_api_key_here
echo. 