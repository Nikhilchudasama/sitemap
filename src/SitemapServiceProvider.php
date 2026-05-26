<?php

namespace Nikhil\Sitemap;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SitemapServiceProvider extends ServiceProvider implements DeferrableProvider
{
    private const PACKAGE_NAME = 'sitemap';

    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        $viewsPath = realpath(__DIR__.'/../resources/views') ?: __DIR__.'/../resources/views';
        $assetsPath = realpath(__DIR__.'/../resources/dist') ?: __DIR__.'/../resources/dist';
        $configFile = realpath(__DIR__.'/../config/sitemap.php') ?: __DIR__.'/../config/sitemap.php';

        $this->loadViewsFrom($viewsPath, self::PACKAGE_NAME);

        $this->publishes([
            $configFile => config_path('sitemap.php'),
        ], self::PACKAGE_NAME.'-config');

        $this->publishes([
            $viewsPath => resource_path('views/vendor/sitemap'),
        ], self::PACKAGE_NAME.'-views');

        $this->publishes([
            $assetsPath => public_path('vendor/sitemap'),
        ], self::PACKAGE_NAME.'-assets');
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/sitemap.php') ?: __DIR__.'/../config/sitemap.php', self::PACKAGE_NAME);

        $this->app->bind(self::PACKAGE_NAME, function (Container $app): Sitemap {
            $config = $app->make('config');

            return new Sitemap(
                $config->get(self::PACKAGE_NAME),
                $app->make('cache.store'),
                $config,
                $app->make('files'),
                $app->make(ResponseFactory::class),
                $app->make('view')
            );
        });

        $this->app->alias(self::PACKAGE_NAME, Sitemap::class);
    }

    /**
     * {@inheritdoc}
     */
    public function provides(): array
    {
        return [self::PACKAGE_NAME, Sitemap::class];
    }
}
