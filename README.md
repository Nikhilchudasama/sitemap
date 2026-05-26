# Laravel Sitemap

[![Total Downloads](https://img.shields.io/packagist/dt/nikhil/laravel-sitemap.svg?style=flat-square)](https://packagist.org/packages/nikhil/laravel-sitemap)
[![License](https://img.shields.io/packagist/l/nikhil/laravel-sitemap.svg?style=flat-square)](https://packagist.org/packages/nikhil/laravel-sitemap)

A powerful and easy-to-use sitemap generator for Laravel. Support for Google News, Images, Videos, and Multilingual sitemaps.

## Features

- [x] Supports Laravel 10, 11, 12, and 13.
- [x] PHP 8.2+ Compatibility.
- [x] Dynamic sitemap generation.
- [x] Automatic caching.
- [x] Support for Big Sitemaps (Sitemap Index).
- [x] Google News, Images, Videos, and Multilingual support.
- [x] Multiple output formats (XML, HTML, TXT).

## Installation

Install the package via composer:

```bash
composer require nikhil/laravel-sitemap
```

(Optional) Publish the configuration file, views, or assets:

```bash
php artisan vendor:publish --tag="sitemap-config"
php artisan vendor:publish --tag="sitemap-views"
php artisan vendor:publish --tag="sitemap-assets"
```

## Quick Start

Generate a simple sitemap in your `routes/web.php`:

```php
use Nikhil\Sitemap\Sitemap;

Route::get('sitemap.xml', function () {
    /** @var Sitemap $sitemap */
    $sitemap = app('sitemap');

    // Add static pages
    $sitemap->add(url('/'), now(), '1.0', 'daily');
    $sitemap->add(url('contact'), now(), '0.7', 'monthly');

    // Add dynamic items from database
    $posts = \App\Models\Post::latest()->get();
    foreach ($posts as $post) {
        $sitemap->add(url($post->slug), $post->updated_at, '0.9', 'weekly');
    }

    return $sitemap->render('xml');
});
```

## Advanced Usage

For more detailed examples, please refer to the [EXAMPLES.md](EXAMPLES.md) file.

### Common Scenarios:

- **[Caching](EXAMPLES.md#caching)**: Speed up your sitemap generation.
- **[Big Sitemaps](EXAMPLES.md#big-sitemaps-sitemap-index)**: Handling more than 50k items.
- **[Save to File](EXAMPLES.md#generating-sitemap-to-a-file)**: Generate sitemap via Artisan commands.
- **[Images & Videos](EXAMPLES.md#advanced-item-options)**: Add media to your sitemap items.
- **[Multilingual](EXAMPLES.md#translations-multilingual)**: Support for `hreflang` tags.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
