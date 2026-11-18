# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0]

### Added

- **Filament 4 and 5 support**, on Livewire 3 and 4 ([#7](https://github.com/Shipu/web-installer/pull/7)).
- Automated tests, run on every push and pull request ([#7](https://github.com/Shipu/web-installer/pull/7)).

### Changed

- **Breaking:** custom steps must import `Filament\Schemas\Components\Wizard\Step` instead of `Filament\Forms\Components\Wizard\Step` ([#7](https://github.com/Shipu/web-installer/pull/7)).
- **Breaking:** requires PHP 8.2+ and Laravel 11.28+ or 12 ([#7](https://github.com/Shipu/web-installer/pull/7)).
- Installer stylesheet rebuilt for Filament 4/5: republish the assets with `php artisan vendor:publish --tag=web-installer-assets --force` ([#7](https://github.com/Shipu/web-installer/pull/7)).
- Session and cache use the file driver until the installation is complete, then the database driver once its tables exist ([#6](https://github.com/Shipu/web-installer/pull/6)).

### Removed

- **Breaking:** Filament 3 support; keep using 1.x for Filament 3 ([#7](https://github.com/Shipu/web-installer/pull/7)).
- Unused JavaScript bundle from the published assets ([#7](https://github.com/Shipu/web-installer/pull/7)).

### Fixed

- Admin name, email and password defaults from `config/installer.php` not being pre-filled ([#7](https://github.com/Shipu/web-installer/pull/7)).
- Installer failing to load with Livewire 4 when the package boots before Livewire ([#7](https://github.com/Shipu/web-installer/pull/7)).
- Installer failing to load when the app's session or cache driver is `database` and its tables don't exist yet ([#6](https://github.com/Shipu/web-installer/pull/6)).

## [1.0.1] - 2023-10-06

### Changed

- **Breaking:** the `redirect_url` closure in `config/installer.php` is replaced by `redirect_route`, which takes a route name ([#2](https://github.com/Shipu/web-installer/pull/2)).
- **Breaking:** `Shipu\WebInstaller\Manager\InstallationManger` renamed to `InstallationManager` ([#2](https://github.com/Shipu/web-installer/pull/2)).
- Validation rules in the environment form can be given as class names, which are instantiated automatically ([#2](https://github.com/Shipu/web-installer/pull/2)).
- Default admin email changed to `admin@example.com` ([#2](https://github.com/Shipu/web-installer/pull/2)).

### Fixed

- `php artisan config:cache` failing because the installer configuration contained closures and objects ([#2](https://github.com/Shipu/web-installer/pull/2)).

## [1.0.0] - 2023-08-27

### Added

- **Web installer**: a four-step wizard at `/installer` that checks server requirements and folder permissions, writes the environment settings to `.env` and creates the admin user.
- Database connection check before saving the environment settings.
- `redirect.if.not.installed` middleware to send visitors to the installer until the app is installed.
- Custom installer steps and a custom installation manager, configurable in `config/installer.php`.
