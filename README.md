# **[Laravel Sitemap] package**

*Laravel Sitemap generator for Laravel.*

* Original this package [Laravelium Sitemap](https://github.com/Laravelium/laravel-sitemap) , I adopted this package and tested with laravel 9.
* PHPTest is not tested

## Notes

- Dev Branches are for development and are **UNSTABLE** (*use on your own risk*)!

## Installation

Run the following command and provide the latest stable version :

```bash
composer require nik/laravel-sitemap
```

*Publish needed assets (styles, views, config files) :*

```bash
php artisan vendor:publish --provider="Nik\Sitemap\SitemapServiceProvider"
```
**Note:** *Composer won't update them after `composer update`, you'll need to do it manually!*

## Examples

- [How to generate dynamic sitemap (with optional caching)](https://github.com/Laravelium/laravel-sitemap/wiki/Dynamic-sitemap)

- [How to generate BIG sitemaps (with more than 1M items)](https://github.com/Laravelium/laravel-sitemap/wiki/Sitemap-index)

- [How to generate sitemap to a file](https://github.com/Laravelium/laravel-sitemap/wiki/Generate-sitemap)

- [How to use multiple sitemaps with sitemap index](https://github.com/Laravelium/laravel-sitemap/wiki/Generate-BIG-sitemaps)

and more in the [Wiki](https://github.com/Laravelium/laravel-sitemap/wiki).

## Contribution guidelines

Before submiting new merge request or creating new issue, please read [contribution guidelines](https://gitlab.com/Laravelium/Sitemap/blob/master/CONTRIBUTING.md).

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
