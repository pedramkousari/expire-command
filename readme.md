# Expire Command Package for Laravel

## Overview

The **Expire Command** package provides a console command for managing expiration dates in a Laravel application. It allows users to set an expiration date, check if the expiration date has passed, and bring the site back up if it has been taken down due to expiration.

## Installation

To install the package, follow these steps:

1. **Add the package to your Laravel project:**

   In your Laravel project's `composer.json`, add the following repository:

   ```json
   "repositories": [
       {
           "type": "path",
           "url": "packages/pedramkousari/expire-command"
       }
   ],
   ```

2. **Require the package:**

   Run the following command in your terminal:

   ```bash
   composer require pedramkousari/expire-command
   ```

3. **Register the Service Provider:**

   In your `config/app.php` file, add the service provider to the `providers` array:

   ```php
   'providers' => [
       // ...
       Pedramkousari\ExpireCommand\Providers\ExpireCommandServiceProvider::class,
   ],
   ```

## Usage

After installing the package, you can use the command in your terminal.

### Command Signature

```bash
php artisan z-abshar:expire {--check}
```

### Options

- `--check`: If this option is provided, the command will check if the expiration date is set and whether it has passed.

### Interactive Menu

If the `--check` option is not provided, an interactive menu will appear with the following options:

1. **Set Expire**: Set a new expiration date.
2. **Check Expire**: Check the current expiration date and its validity.
3. **Up Site**: Bring the site back up if it has been taken down due to expiration.

### Setting Expiration Date

When setting the expiration date, you will be prompted to enter a date. The date must be in the future. If the date is invalid or not greater than the current date, an error message will be displayed.

### Checking Expiration Date

When checking the expiration date, the command will inform you if the date is set and whether it has expired. If the expiration date has passed, the command will call the `down` command to take the site down.

### Bringing the Site Up

If the site is down due to expiration, you can use the "Up Site" option to delete the expiration date file and bring the site back up.

## License

This package is open-source and available under the [MIT License](LICENSE).
