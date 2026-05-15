# Laravel Sitemap Examples

This document provides detailed examples for various use cases of the Laravel Sitemap package.

## Table of Contents
- [Dynamic Sitemap](#dynamic-sitemap)
- [Caching](#caching)
- [Generating Sitemap to a File](#generating-sitemap-to-a-file)
- [Big Sitemaps (Sitemap Index)](#big-sitemaps-sitemap-index)
- [Advanced Item Options](#advanced-item-options)
    - [Images](#images)
    - [Videos](#videos)
    - [Google News](#google-news)
    - [Translations (Multilingual)](#translations-multilingual)

---

## Dynamic Sitemap

The most common way to use this package is to generate a sitemap dynamically on a specific route.

```php
// routes/web.php

use Nik\Sitemap\Sitemap;

Route::get('sitemap', function () {
    /** @var Sitemap $sitemap */
    $sitemap = app('sitemap');

    // Add items
    $sitemap->add(url('/'), '2024-05-15T00:00:00+00:00', '1.0', 'daily');
    $sitemap->add(url('about'), '2024-05-15T00:00:00+00:00', '0.8', 'weekly');

    // Add dynamic items from database
    $posts = DB::table('posts')->orderBy('created_at', 'desc')->get();

    foreach ($posts as $post) {
        $sitemap->add(url($post->slug), $post->updated_at, '0.9', 'monthly');
    }

    // Render the sitemap (default is xml)
    return $sitemap->render('xml');
});
```

---

## Caching

You can easily cache your sitemap to improve performance.

```php
Route::get('sitemap', function () {
    /** @var Sitemap $sitemap */
    $sitemap = app('sitemap');

    // Set cache key, duration (in minutes), and enable cache
    $sitemap->setCache('laravel.sitemap.posts', 60);

    // Check if sitemap is already cached
    if (!$sitemap->isCached()) {
        $sitemap->add(url('/'), '2024-05-15T00:00:00+00:00', '1.0', 'daily');
        
        $posts = DB::table('posts')->get();
        foreach ($posts as $post) {
            $sitemap->add(url($post->slug), $post->updated_at, '0.9', 'monthly');
        }
    }

    return $sitemap->render('xml');
});
```

---

## Generating Sitemap to a File

If you have a large site, you might want to generate the sitemap and save it as a file (e.g., via a Cron job or Artisan command).

```php
// app/Console/Commands/GenerateSitemap.php

public function handle()
{
    /** @var Sitemap $sitemap */
    $sitemap = app('sitemap');

    $sitemap->add(url('/'), '2024-05-15T00:00:00+00:00', '1.0', 'daily');
    
    // ... add more items ...

    // Store sitemap to public/sitemap.xml
    $sitemap->store('xml', 'sitemap');
    
    $this->info('Sitemap generated successfully!');
}
```

---

## Big Sitemaps (Sitemap Index)

If you have more than 50,000 items, you should use a Sitemap Index.

```php
Route::get('sitemap', function () {
    /** @var Sitemap $sitemap */
    $sitemap = app('sitemap');

    // Add references to other sitemaps
    $sitemap->addSitemap(url('sitemap-posts.xml'));
    $sitemap->addSitemap(url('sitemap-categories.xml'));

    return $sitemap->render('sitemapindex');
});
```

---

## Advanced Item Options

### Images
```php
$images = [
    ['url' => url('images/post1.jpg'), 'title' => 'Post 1 Image', 'caption' => 'An amazing image'],
    ['url' => url('images/post1-featured.jpg'), 'title' => 'Featured Image'],
];

$sitemap->add(url('post-1'), '2024-05-15T00:00:00+00:00', '0.9', 'monthly', $images);
```

### Videos
```php
$videos = [
    [
        'title' => 'Video Title',
        'description' => 'Video Description',
        'thumbnail_loc' => url('video-thumb.jpg'),
        'content_loc' => url('video.mp4'),
    ]
];

$sitemap->add(url('video-page'), '2024-05-15T00:00:00+00:00', '0.9', 'monthly', [], null, [], $videos);
```

### Google News
```php
$googlenews = [
    'sitename' => 'My News Site',
    'language' => 'en',
    'publication_date' => '2024-05-15',
    'access' => 'Subscription', // Optional: Registration, Subscription
    'genres' => 'PressRelease, Blog', // Optional: PressRelease, Blog, Satire, Opinion, UserGenerated
];

$sitemap->add(url('news-article'), '2024-05-15', '0.9', 'monthly', [], 'Article Title', [], [], $googlenews);
```

### Translations (Multilingual)
```php
$translations = [
    ['language' => 'fr', 'url' => url('fr/page')],
    ['language' => 'de', 'url' => url('de/page')],
];

$sitemap->add(url('en/page'), '2024-05-15', '0.9', 'monthly', [], null, $translations);
```
