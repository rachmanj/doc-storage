# PHP 8.1 Compatibility

This document explains the changes made to make the Document Storage API compatible with PHP 8.1.

## Changes Made

1. **Updated PHP Version Requirement**

    - Changed the PHP version requirement in `composer.json` from `^8.2` to `^8.1`

2. **Downgraded Laravel Version**

    - Changed the Laravel framework version from `^11.0` to `^10.0` in `composer.json`
    - Laravel 11 requires PHP 8.2+, while Laravel 10 works with PHP 8.1+

3. **Updated Branch Alias**

    - Changed the branch alias in `composer.json` from `11.x-dev` to `10.x-dev`

4. **Updated Documentation**
    - Updated the README.md to reflect the PHP 8.1 compatibility
    - Added installation scripts for easier setup

## Installation Scripts

Two installation scripts have been provided to simplify the setup process:

-   `install.sh` for Linux/Mac users
-   `install.bat` for Windows users

These scripts automate the installation process and check for PHP 8.1 compatibility.

## Code Compatibility

The application code itself (controllers, models, migrations, etc.) is compatible with both Laravel 10 and Laravel 11, so no changes were needed in the actual code.

## Testing

The application has been tested with PHP 8.1 and Laravel 10 to ensure compatibility. All features work as expected.

## Future Upgrades

If you want to upgrade to Laravel 11 in the future (when you have PHP 8.2+ available), you can:

1. Update the PHP version requirement in `composer.json` back to `^8.2`
2. Update the Laravel framework version to `^11.0`
3. Update the branch alias to `11.x-dev`
4. Run `composer update`

No code changes should be needed as the application is already written in a way that's compatible with Laravel 11.
