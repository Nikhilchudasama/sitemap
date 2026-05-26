# Changelog

All notable changes to `nikhil/laravel-sitemap` will be documented in this file.

## Unreleased

### Added

- Added Spatie-style package structure with flat `src/`, `config/`, `resources/views`, and `resources/dist` directories.
- Added Composer scripts for static analysis, formatting, Rector dry-runs, and tests.
- Added Pint, Rector, PHPStan, Larastan, Pest, and Testbench tooling for ongoing maintenance.
- Added publish tags for config, views, and assets: `sitemap-config`, `sitemap-views`, and `sitemap-assets`.

### Changed

- Preserved the public `Nikhil\Sitemap` namespace while reorganizing package internals.
- Updated the service provider to load config, views, and publishable assets from the new package structure.
- Updated PHPStan to analyze package source and config without analyzing Pest test closures.
- Updated Rector to use Composer-based Laravel rules for easier future Laravel upgrades.
- Updated package tests to cover service registration, package resources, publishing tags, and `addItem()` list handling.

### Fixed

- Fixed `Sitemap::addItem()` handling for a single-item list.
- Made required `Sitemap` constructor dependencies non-nullable.

## 9.0.1 - 2026-05-15

### Added

- Added comprehensive `EXAMPLES.md` file with detailed usage scenarios.
- Added quick-start guide in `README.md`.

### Changed

- Cleaned up `README.md` to remove clutter and outdated links.
- Updated support links in `composer.json` to point to the correct repository.

## 9.0.0 - 2026-05-13

### Added

- Added support for Laravel 11, 12, and 13.
- Added support for PHP 8.2 and 8.3.
- Added modern PHP syntax, typed properties, and constructor property promotion.
- Added PSR-4 autoloading support.
- Added PHPUnit and Orchestra Testbench to development dependencies.

### Fixed

- Fixed title assignment after escaping in `Sitemap.php`.
- Updated tests to match the package namespace.
- Made the `$sloc` property nullable in `Model` to fix test type errors.

## 8.0.1 - 2020-09-10

### Added

- Added support for Laravel 8.
- Added new branch for development `8.x-dev`.

### Fixed

- Minor bug fixes and optimizations.

## 7.0.1 - 2020-03-21

### Added

- Added support for Laravel 7.
- Added new branch for development `7.x-dev`.

### Fixed

- Minor bug fixes and optimizations.

## 6.0.1 - 2019-09-03

### Added

- Added support for Laravel 6.
- Added new branch for development `6.x-dev`.

### Fixed

- Minor bug fixes and optimizations.

## 3.1.1 - 2018-09-04

### Added

- Added support for Laravel 5.8.
- Added new branch for development `3.1.x-dev`.

### Fixed

- Minor bug fixes and optimizations.

## 3.0.1 - 2018-09-04

### Added

- Added new Packagist repository for `laravelium/sitemap`.
- Added new branch for development `3.0.x-dev`.
- Added GitLab CI/CD testing with code coverage.
- Added StyleCI coding style testing.
- Added support for gzip compression.
- Added release changelog.
- Added contributing guidelines.

### Changed

- Changed package name from `roumen/sitemap` to `laravelium/sitemap`.

### Fixed

- Minor bug fixes and optimizations.

## 2.8.3 - 2018-09-04

### Added

- Added support for gzip compression.
- Added release changelog.
- Added contributing guidelines.

### Fixed

- Minor bug fixes and optimizations.

## 2.8.2 - 2018-08-27

### Added

- Added new Packagist repository for `laravelium/sitemap`.
- Added new branch for development `2.8.x-dev`.
- Added GitLab CI/CD testing with code coverage.
- Added StyleCI coding style testing.

### Changed

- Changed package name from `roumen/sitemap` to `laravelium/sitemap`.

### Fixed

- Minor bug fixes and optimizations.

## 2.7.3 - 2018-09-04

### Added

- Added new Packagist repository for `laravelium/sitemap`.
- Added new branch for development `2.7.x-dev`.
- Added GitLab CI/CD testing with code coverage.
- Added StyleCI coding style testing.
- Added support for gzip compression.
- Added release changelog.
- Added contributing guidelines.

### Changed

- Changed package name from `roumen/sitemap` to `laravelium/sitemap`.

### Fixed

- Minor bug fixes and optimizations.
