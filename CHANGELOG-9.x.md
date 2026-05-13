# Change log

## v9.0.0 (2026-05-13)

### Added
- Support for Laravel 11, 12, and 13.
- Support for PHP 8.2 and 8.3.
- Modern PHP syntax (typed properties, constructor property promotion).
- PSR-4 autoloading support.
- Added `phpunit/phpunit` and `orchestra/testbench` to `require-dev`.

### Fixed
- Fixed bug where title was not correctly assigned after escaping in `Sitemap.php`.
- Updated tests to match the `Nik\Sitemap` namespace.
- Made `$sloc` property nullable in `Model` to fix type errors in tests.
