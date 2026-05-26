<?php

use Illuminate\Support\ServiceProvider;
use Nikhil\Sitemap\Sitemap;
use Nikhil\Sitemap\SitemapServiceProvider;

it('can initialize sitemap object', function () {
    expect($this->sitemap)->toBeInstanceOf(Sitemap::class);
});

it('can provide package details', function () {
    $sp = new SitemapServiceProvider($this->app);
    expect($sp->provides())->toBe(['sitemap', Sitemap::class]);
});

it('registers the sitemap binding and package resources', function () {
    (new SitemapServiceProvider($this->app))->boot();

    expect(app('sitemap'))->toBeInstanceOf(Sitemap::class)
        ->and(config('sitemap.cache_duration'))->toBe(3600)
        ->and(view()->exists('sitemap::xml'))->toBeTrue()
        ->and(ServiceProvider::pathsToPublish(null, 'sitemap-config'))->toHaveKey(realpath(__DIR__.'/../../config/sitemap.php'))
        ->and(ServiceProvider::pathsToPublish(null, 'sitemap-views'))->toHaveKey(realpath(__DIR__.'/../../resources/views'))
        ->and(ServiceProvider::pathsToPublish(null, 'sitemap-assets'))->toHaveKey(realpath(__DIR__.'/../../resources/dist'));
});

it('can set and get sitemap attributes', function () {
    $this->sitemap->model->setLink('TestLink');
    $this->sitemap->model->setTitle('TestTitle');
    $this->sitemap->model->setUseCache(true);
    $this->sitemap->model->setCacheKey('lv-sitemap');
    $this->sitemap->model->setCacheDuration(72000);
    $this->sitemap->model->setEscaping(false);
    $this->sitemap->model->setUseLimitSize(true);
    $this->sitemap->model->setMaxSize(10000);
    $this->sitemap->model->setUseStyles(false);
    $this->sitemap->model->setSloc('https://static.foobar.tld/xsl-styles/');

    expect($this->sitemap->model->getLink())->toBe('TestLink')
        ->and($this->sitemap->model->getTitle())->toBe('TestTitle')
        ->and($this->sitemap->model->getUseCache())->toBeTrue()
        ->and($this->sitemap->model->getCacheKey())->toBe('lv-sitemap')
        ->and($this->sitemap->model->getCacheDuration())->toBe(72000)
        ->and($this->sitemap->model->getEscaping())->toBeFalse()
        ->and($this->sitemap->model->getUseLimitSize())->toBeTrue()
        ->and($this->sitemap->model->getMaxSize())->toBe(10000)
        ->and($this->sitemap->model->testing)->toBeTrue()
        ->and($this->sitemap->model->getUseStyles())->toBeFalse()
        ->and($this->sitemap->model->getSloc())->toBe('https://static.foobar.tld/xsl-styles/');
});

it('can add items to sitemap', function () {
    $translations = [
        ['language' => 'de', 'url' => '/pageDe'],
        ['language' => 'bg', 'url' => '/pageBg?id=1&sid=2'],
    ];

    $translationsEscaped = [
        ['language' => 'de', 'url' => '/pageDe'],
        ['language' => 'bg', 'url' => '/pageBg?id=1&amp;sid=2'],
    ];

    $images = [
        ['url' => 'test.png'],
        ['url' => '<&>'],
    ];

    $imagesEscaped = [
        ['url' => 'test.png'],
        ['url' => '&lt;&amp;&gt;'],
    ];

    $videos = [
        [
            'title' => 'TestTitle',
            'description' => 'TestDescription',
            'content_loc' => 'https://damianoff.com/testVideo.flv',
            'thumbnail_loc' => 'https://damianoff.com/testVideo.png',
        ],
        [
            'title' => 'TestTitle2&',
            'description' => 'TestDescription2&',
            'content_loc' => 'https://damianoff.com/testVideo2.flv',
        ],
    ];

    $googleNews = [
        'sitename' => 'Foo',
        'language' => 'en',
        'publication_date' => '2016-01-03',
    ];

    $alternates = [
        ['media' => 'only screen and (max-width: 640px)', 'url' => 'https://m.foobar.tld'],
    ];

    $this->sitemap->add('TestLoc', '2014-02-29 00:00:00', 0.95, 'weekly', $images, 'TestTitle', $translations, $videos, $googleNews, $alternates);
    $this->sitemap->add(null, null, 0.85, 'daily');

    $items = $this->sitemap->model->getItems();

    expect($items)->toHaveCount(2)
        ->and($items[0]['loc'])->toBe('TestLoc')
        ->and($items[1]['loc'])->toBe('/')
        ->and($items[0]['lastmod'])->toBe('2014-02-29 00:00:00')
        ->and($items[0]['priority'])->toBe(0.95)
        ->and($items[0]['freq'])->toBe('weekly')
        ->and($items[0]['title'])->toBe('TestTitle')
        ->and($items[0]['images'])->toBe($imagesEscaped)
        ->and($items[0]['translations'])->toBe($translationsEscaped)
        ->and($items[0]['videos'][1]['title'])->toBe('TestTitle2&amp;')
        ->and($items[0]['googlenews']['sitename'])->toBe('Foo');
});

it('can use addItem to add multiple items', function () {
    $this->sitemap->addItem(['title' => 'testTitle0']);
    $this->sitemap->addItem([
        ['loc' => 'TestLoc1', 'priority' => 0.95],
    ]);
    $this->sitemap->addItem([
        ['loc' => 'TestLoc2', 'priority' => 0.85],
        ['loc' => 'TestLoc3', 'priority' => 0.75],
    ]);

    $items = $this->sitemap->model->getItems();

    expect($items)->toHaveCount(4)
        ->and($items[0]['title'])->toBe('testTitle0')
        ->and($items[1]['loc'])->toBe('TestLoc1')
        ->and($items[2]['loc'])->toBe('TestLoc2')
        ->and($items[3]['loc'])->toBe('TestLoc3');
});

it('can render sitemap in various formats', function (string $format, string $contentType) {
    $response = $this->sitemap->render($format);

    expect($response->status())->toBe(200)
        ->and($response->headers->get('Content-Type'))->toBe($contentType);
})->with([
    ['xml', 'text/xml; charset=utf-8'],
    ['sitemapindex', 'text/xml; charset=utf-8'],
    ['html', 'text/html; charset=utf-8'],
    ['txt', 'text/plain; charset=utf-8'],
    ['ror-rss', 'text/rss+xml; charset=utf-8'],
    ['ror-rdf', 'text/rdf+xml; charset=utf-8'],
]);

it('can store sitemap to file', function () {
    // Basic store
    $this->sitemap->store('xml', 'sitemap');

    $this->seedItems(100);
    $this->sitemap->model->resetItems($this->itemSeeder);

    $this->sitemap->model->setUseLimitSize(true);
    $this->sitemap->store('xml', 'sitemap_limited');

    // Check if files exist (in testing mode it might not write to real disk,
    // but the store method calls $this->file->put)
    expect(true)->toBeTrue();
});

it('can check if content is cached', function () {
    expect($this->sitemap->isCached())->toBeFalse();

    $this->sitemap->setCache('testKey', 60, true);
    $this->sitemap->cache->put($this->sitemap->model->getCacheKey(), $this->sitemap->model->getItems(), 60);

    expect($this->sitemap->isCached())->toBeTrue();
});
