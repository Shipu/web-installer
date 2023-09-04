# Laravel Web Installer
Laravel Web Installer is a Laravel package that allows you to install your application easily, without having to worry about setting up your environment before starting with the installation process.

[![Latest Stable Version](http://poser.pugx.org/shipu/web-installer/v)](https://packagist.org/packages/shipu/web-installer) [![Total Downloads](http://poser.pugx.org/shipu/web-installer/downloads)](https://packagist.org/packages/shipu/web-installer) [![Latest Unstable Version](http://poser.pugx.org/shipu/web-installer/v/unstable)](https://packagist.org/packages/shipu/web-installer) [![License](http://poser.pugx.org/shipu/web-installer/license)](https://packagist.org/packages/shipu/web-installer) [![PHP Version Require](http://poser.pugx.org/shipu/web-installer/require/php)](https://packagist.org/packages/shipu/web-installer)
## Installation 
```bash
composer require shipu/web-installer
```
then publish the assets
```bash
php artisan vendor:publish --tag=web-installer-assets
 ```

## Screenshots
![Server Requirements](https://raw.githubusercontent.com/Shipu/web-installer/master/screenshots/installer_1.png)

![Folder Permissions](https://raw.githubusercontent.com/Shipu/web-installer/master/screenshots/installer_2.png)

![Environment](https://raw.githubusercontent.com/Shipu/web-installer/master/screenshots/installer_3.png)

![Application Settings](https://raw.githubusercontent.com/Shipu/web-installer/master/screenshots/installer_4.png)

## Add New Step
You can add new step in installer. For this you have to create a new class and implement `Shipu\WebInstaller\Concerns\StepContract` class. Eg:

```php
<?php

namespace Your\Namespace;

use Filament\Forms\Components\Wizard\Step;
use Shipu\WebInstaller\Concerns\StepContract;

class Overview implements StepContract
{
    public static function make(): Step
    {
        return Step::make('overview')
            ->label('Overview')
            ->schema([
             // Add Filament Fields Here
            ]);
    }
}
```
For `Step` documentation please visit [Filament Forms](https://filamentphp.com/docs/3.x/forms/layout/wizard)

Then you have to add this class in `config/installer.php` Eg:

```php
//...
'steps' => [
    Overview::class, // <-- Add Here
    //...
],
//...
```
Note: you have to publish config file first. More details in [Configuration](#configuration) section.

## Protect Routes

Protect other routes if not installed then you can apply the middleware to a route or route-group. Eg:

```php
Route::group(['middleware' => 'redirect.if.not.installed'], function () {
    Route::get('/', function () {
        return view('welcome');
    });
});
```

In Filament, if you want to protect all admin panel routes then you have to add middleware in panel service provider. Eg:

```php
public function panel(Panel $panel): Panel
{
    return $panel
        ...
        ->middleware([
            \Shipu\WebInstaller\Middleware\RedirectIfNotInstalled::class,
            ...
        ]);
}
```

## Configuration

you can modify almost everything in this package. For this you have to publish the config file. Eg:

```bash
php artisan vendor:publish --tag=web-installer-config
```
