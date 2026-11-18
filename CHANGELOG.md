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

### Removed

- **Breaking:** Filament 3 support; keep using 1.x for Filament 3 ([#7](https://github.com/Shipu/web-installer/pull/7)).
- Unused JavaScript bundle from the published assets ([#7](https://github.com/Shipu/web-installer/pull/7)).

### Fixed

- Admin name, email and password defaults from `config/installer.php` not being pre-filled ([#7](https://github.com/Shipu/web-installer/pull/7)).
- Installer failing to load with Livewire 4 when the package boots before Livewire ([#7](https://github.com/Shipu/web-installer/pull/7)).
