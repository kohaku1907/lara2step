# Lara2Step

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kohaku1907/lara2step.svg?style=flat-square)](https://packagist.org/packages/kohaku1907/lara2step)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kohaku1907/lara2step/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/kohaku1907/lara2step/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/kohaku1907/lara2step/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/kohaku1907/lara2step/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kohaku1907/lara2step.svg?style=flat-square)](https://packagist.org/packages/kohaku1907/lara2step)

Lara2Step is a Laravel package that provides two-step authentication to your Laravel applications.

## Installation

Install the package via composer:

```bash
composer require kohaku1907/lara2step
```

Publish and run the migrations with:

```bash
php artisan vendor:publish --tag="2step-migrations"
php artisan migrate
```

Publish the config file with:

```bash
php artisan vendor:publish --tag="2step-config"
```

This is the contents of the published config file:

```php
return [
    'default_channel' => 'email', // email, sms
    'table_name' => 'two_step_auths', // table name
    'code_length' => 4, // code length
    'numeric_code' => false, // numeric code only
    'confirm_key' => '_2fa', // session key name
    'timeout' => 300, // timeout of verifed session in minutes
    'max_attempts' => 5, // max attempts
    'exceed_countdown_minutes' => 1440, // exceed countdown in minutes
    'resend_code_seconds' => 60, // resend code in seconds
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="2step-views"
```

## Usage

The Lara2Step package can be integrated into your Laravel application by following these steps:

1. Add the `TwoStepAuthenticatable` trait to your `User` model:
2. Add the `TwoStepAuthenticatable` trait to your `User` model:

Here is an example of a `User` model:

```php
use Kohaku1907\Lara2step\Contracts\TwoStepAuthenticatable;
use Kohaku1907\Lara2step\TwoStepAuthentication;

class User extends Authenticatable implements TwoStepAuthenticatable {
    use TwoStepAuthentication;

    public function registerTwoStepAuthentication(): void
    {
        $this->configureForceEnable('email');
        $this->configureCodeFormat(length: 4, numericCode: true);
    }
}
```
In the `registerTwoStepAuthentication` method, you can configure the two-step authentication settings for the user. The following methods are available:

- `configureForceEnable(string $channel)`: Force enable two-step authentication for the user. The user will not be able to disable two-step authentication.
- `configureCodeFormat(int $length, bool $numericCode)`: Configure the code format for the user. The code length and whether the code should be numeric or not can be configured.

3. Add the alias middleware to routes that should be protected by two-step authentication:

```php
Route::get('/dashboard', function () {
    // Only verified users...
})->middleware('2step');
```
The middleware will redirect the user to the named route 2step.confirm by default if the user is not verified. Lara2step comes with TwoStepController and default views for quick start. You can publish the views using `php artisan vendor:publish --tag="2step-views"` and customize them to your needs.

```php
use Kohaku1907\Lara2step\Http\Controllers\TwoStepController;
use Illuminate\Support\Facades\Route;

Route::get('2fa-confirm', [TwoStepController::class, 'form'])
    ->name('2step.confirm');
Route::post('2fa-confirm', [TwoStepController::class, 'confirm']);
Route::post('2fa-resend', [TwoStepController::class, 'resend'])
    ->name('2step.resend');
```





## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
