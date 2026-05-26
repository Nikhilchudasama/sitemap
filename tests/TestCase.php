<?php

namespace Nikhil\Sitemap\Test;

use Nikhil\Sitemap\Sitemap;
use Nikhil\Sitemap\SitemapServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * @var array
     */
    protected $itemSeeder = [];

    protected function getPackageProviders($app)
    {
        return [SitemapServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return ['Sitemap' => Sitemap::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $config = [
            'sitemap.use_cache' => false,
            'sitemap.cache_key' => 'Laravel.Sitemap.',
            'sitemap.cache_duration' => 3600,
            'sitemap.testing' => true,
            'sitemap.styles_location' => '/styles/',
        ];

        config($config);

        $this->sitemap = $this->app->make(Sitemap::class);
    }

    protected function seedItems($n = 50001)
    {
        $this->itemSeeder = [];

        for ($i = 0; $i < $n; $i++) {
            $this->itemSeeder[] = [
                'loc' => 'TestLoc'.$i,
                'lastmod' => '2018-06-11 20:00:00',
                'priority' => 0.95,
                'freq' => 'daily',
                'googlenews' => [
                    'sitename' => 'Foo',
                    'language' => 'en',
                    'publication_date' => '2018-08-25',
                ],
                'title' => 'TestTitle',
            ];
        }
    }
}
