<?php

namespace Nikhil\Sitemap;

use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;

/**
 * Sitemap class for laravel-sitemap package.
 *
 * @author Rumen Damyanov <r@alfamatter.com>
 *
 * @version 9.0.1
 *
 * @link https://github.com/Nikhilchudasama/sitemap
 *
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Sitemap
{
    /**
     * Model instance.
     */
    public Model $model;

    /**
     * Using constructor we populate our model from configuration file
     * and loading dependencies.
     */
    public function __construct(
        array $config,
        /**
         * CacheRepository instance.
         */
        public CacheRepository $cache,
        /**
         * ConfigRepository instance.
         */
        protected ConfigRepository $configRepository,
        /**
         * Filesystem instance.
         */
        protected Filesystem $file,
        /**
         * ResponseFactory instance.
         */
        protected ResponseFactory $response,
        /**
         * ViewFactory instance.
         */
        protected ViewFactory $view
    ) {
        $this->model = new Model($config);
    }

    /**
     * Set cache options.
     *
     * @param  string  $key
     * @param  DateInterval|DateTimeInterface|int|null  $duration
     */
    public function setCache($key = null, $duration = null, bool $useCache = true): void
    {
        $this->model->setUseCache($useCache);

        if ($key !== null) {
            $this->model->setCacheKey($key);
        }

        if ($duration !== null) {
            $this->model->setCacheDuration($duration);
        }
    }

    /**
     * Checks if content is cached.
     */
    public function isCached(): bool
    {
        if ($this->model->getUseCache()) {
            if ($this->cache->has($this->model->getCacheKey())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add new sitemap item to $items array.
     *
     * @param  string  $loc
     * @param  string  $lastmod
     * @param  string  $priority
     * @param  string  $freq
     * @param  array  $images
     * @param  string  $title
     * @param  array  $translations
     * @param  array  $videos
     * @param  array  $googlenews
     * @param  array  $alternates
     */
    public function add($loc, $lastmod = null, $priority = null, $freq = null, $images = [], $title = null, $translations = [], $videos = [], $googlenews = [], $alternates = []): void
    {
        $params = [
            'loc' => $loc,
            'lastmod' => $lastmod,
            'priority' => $priority,
            'freq' => $freq,
            'images' => $images,
            'title' => $title,
            'translations' => $translations,
            'videos' => $videos,
            'googlenews' => $googlenews,
            'alternates' => $alternates,
        ];

        $this->addItem($params);
    }

    /**
     * Add new sitemap one or multiple items to $items array.
     *
     * @param  array  $params
     */
    public function addItem($params = []): void
    {

        // if it is a list of sitemap items
        if (isset($params[0]) && is_array($params[0]) && array_is_list($params)) {
            foreach ($params as $a) {
                $this->addItem($a);
            }

            return;
        }

        $params = array_replace([
            'loc' => '/',
            'lastmod' => null,
            'priority' => null,
            'freq' => null,
            'title' => null,
            'images' => [],
            'translations' => [],
            'alternates' => [],
            'videos' => [],
            'googlenews' => [],
        ], $params);

        $loc = $params['loc'] ?? '/';
        $lastmod = $params['lastmod'] ?? null;
        $priority = $params['priority'] ?? null;
        $freq = $params['freq'] ?? null;
        $title = $params['title'] ?? null;
        $images = $params['images'] ?? [];
        $translations = $params['translations'] ?? [];
        $alternates = $params['alternates'] ?? [];
        $videos = $params['videos'] ?? [];
        $googlenews = $params['googlenews'] ?? [];

        // escaping
        if ($this->model->getEscaping()) {
            $loc = htmlentities($loc, ENT_XML1);

            if ($title !== null) {
                $title = htmlentities($title, ENT_XML1);
            }

            if ($images) {
                foreach ($images as $k => $image) {
                    foreach ($image as $key => $value) {
                        $images[$k][$key] = htmlentities((string) $value, ENT_XML1);
                    }
                }
            }

            if ($translations) {
                foreach ($translations as $k => $translation) {
                    foreach ($translation as $key => $value) {
                        $translations[$k][$key] = htmlentities((string) $value, ENT_XML1);
                    }
                }
            }

            if ($alternates) {
                foreach ($alternates as $k => $alternate) {
                    foreach ($alternate as $key => $value) {
                        $alternates[$k][$key] = htmlentities((string) $value, ENT_XML1);
                    }
                }
            }

            if ($videos) {
                foreach ($videos as $k => $video) {
                    if (! empty($video['title'])) {
                        $videos[$k]['title'] = htmlentities((string) $video['title'], ENT_XML1);
                    }

                    if (! empty($video['description'])) {
                        $videos[$k]['description'] = htmlentities((string) $video['description'], ENT_XML1);
                    }
                }
            }

            if ($googlenews) {
                if (isset($googlenews['sitename'])) {
                    $googlenews['sitename'] = htmlentities($googlenews['sitename'], ENT_XML1);
                }
            }
        }

        $googlenews['sitename'] ??= '';
        $googlenews['language'] ??= 'en';
        $googlenews['publication_date'] ??= date('Y-m-d H:i:s');

        $this->model->setItems([
            'loc' => $loc,
            'lastmod' => $lastmod,
            'priority' => $priority,
            'freq' => $freq,
            'images' => $images,
            'title' => $title,
            'translations' => $translations,
            'videos' => $videos,
            'googlenews' => $googlenews,
            'alternates' => $alternates,
        ]);
    }

    /**
     * Add new sitemap to $sitemaps array.
     *
     * @param  string  $loc
     * @param  string  $lastmod
     */
    public function addSitemap($loc, $lastmod = null): void
    {
        $this->model->setSitemaps([
            'loc' => $loc,
            'lastmod' => $lastmod,
        ]);
    }

    /**
     * Add new sitemap to $sitemaps array.
     */
    public function resetSitemaps(array $sitemaps = []): void
    {
        $this->model->resetSitemaps($sitemaps);
    }

    /**
     * Returns document with all sitemap items from $items array.
     *
     * @param  string  $format  (options: xml, html, txt, ror-rss, ror-rdf, google-news)
     * @param  string  $style  (path to custom xls style like '/styles/xsl/xml-sitemap.xsl')
     * @return Response
     */
    public function render($format = 'xml', $style = null)
    {
        // limit size of sitemap
        if ($this->model->getMaxSize() > 0 && count($this->model->getItems()) > $this->model->getMaxSize()) {
            $this->model->limitSize($this->model->getMaxSize());
        } elseif ($format == 'google-news' && count($this->model->getItems()) > 1000) {
            $this->model->limitSize(1000);
        } elseif ($format != 'google-news' && count($this->model->getItems()) > 50000) {
            $this->model->limitSize();
        }

        $data = $this->generate($format, $style);

        return $this->response->make($data['content'], 200, $data['headers']);
    }

    /**
     * Generates document with all sitemap items from $items array.
     *
     * @param  string  $format  (options: xml, html, txt, ror-rss, ror-rdf, sitemapindex, google-news)
     * @param  string  $style  (path to custom xls style like '/styles/xsl/xml-sitemap.xsl')
     * @return array
     */
    public function generate($format = 'xml', $style = null)
    {
        // check if caching is enabled, there is a cached content and its duration isn't expired
        if ($this->isCached()) {
            ($format == 'sitemapindex') ? $this->model->resetSitemaps($this->cache->get($this->model->getCacheKey())) : $this->model->resetItems($this->cache->get($this->model->getCacheKey()));
        } elseif ($this->model->getUseCache()) {
            ($format == 'sitemapindex') ? $this->cache->put($this->model->getCacheKey(), $this->model->getSitemaps(), $this->model->getCacheDuration()) : $this->cache->put($this->model->getCacheKey(), $this->model->getItems(), $this->model->getCacheDuration());
        }

        if (! $this->model->getLink()) {
            $this->model->setLink($this->configRepository->get('app.url'));
        }

        if (! $this->model->getTitle()) {
            $this->model->setTitle('Sitemap for '.$this->model->getLink());
        }

        $channel = [
            'title' => $this->model->getTitle(),
            'link' => $this->model->getLink(),
        ];

        // check if styles are enabled
        if ($this->model->getUseStyles()) {
            if ($style === null && $this->model->getSloc() != null && file_exists(public_path($this->model->getSloc().$format.'.xsl'))) {
                // use style from your custom location
                $style = $this->model->getSloc().$format.'.xsl';
            }
        } else {
            // don't use style
            $style = null;
        }

        return match ($format) {
            'ror-rss' => ['content' => $this->view->make('sitemap::ror-rss', ['items' => $this->model->getItems(), 'channel' => $channel, 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/rss+xml; charset=utf-8']],
            'ror-rdf' => ['content' => $this->view->make('sitemap::ror-rdf', ['items' => $this->model->getItems(), 'channel' => $channel, 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/rdf+xml; charset=utf-8']],
            'html' => ['content' => $this->view->make('sitemap::html', ['items' => $this->model->getItems(), 'channel' => $channel, 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/html; charset=utf-8']],
            'txt' => ['content' => $this->view->make('sitemap::txt', ['items' => $this->model->getItems(), 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/plain; charset=utf-8']],
            'sitemapindex' => ['content' => $this->view->make('sitemap::sitemapindex', ['sitemaps' => $this->model->getSitemaps(), 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/xml; charset=utf-8']],
            default => ['content' => $this->view->make('sitemap::'.$format, ['items' => $this->model->getItems(), 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/xml; charset=utf-8']],
        };
    }

    /**
     * Generate sitemap and store it to a file.
     *
     * @param  string  $format  (options: xml, html, txt, ror-rss, ror-rdf, sitemapindex, google-news)
     * @param  string  $filename  (without file extension, may be a path like 'sitemaps/sitemap1' but must exist)
     * @param  string  $path  (path to store sitemap like '/www/site/public')
     * @param  string  $style  (path to custom xls style like '/styles/xsl/xml-sitemap.xsl')
     */
    public function store($format = 'xml', string $filename = 'sitemap', $path = null, $style = null): void
    {
        // turn off caching for this method
        $this->model->setUseCache(false);

        // use correct file extension
        $fe = (in_array($format, ['txt', 'html'], true)) ? $format : 'xml';

        if ($this->model->getUseGzip() == true) {
            $fe = $fe.'.gz';
        }

        // use custom size limit for sitemaps
        if ($this->model->getMaxSize() > 0 && count($this->model->getItems()) > $this->model->getMaxSize()) {
            if ($this->model->getUseLimitSize()) {
                // limit size
                $this->model->limitSize($this->model->getMaxSize());
                $data = $this->generate($format, $style);
            } else {
                // use sitemapindex and generate partial sitemaps
                foreach (array_chunk($this->model->getItems(), $this->model->getMaxSize()) as $key => $item) {
                    // reset current items
                    $this->model->resetItems($item);

                    // generate new partial sitemap
                    $this->store($format, $filename.'-'.$key, $path, $style);

                    // add sitemap to sitemapindex
                    if ($path != null) {
                        // if using custom path generate relative urls for sitemaps in the sitemapindex
                        $this->addSitemap($filename.'-'.$key.'.'.$fe);
                    } else {
                        // else generate full urls based on app's domain
                        $this->addSitemap(url($filename.'-'.$key.'.'.$fe));
                    }
                }

                $data = $this->generate('sitemapindex', $style);
            }
        } elseif (($format != 'google-news' && count($this->model->getItems()) > 50000) || ($format == 'google-news' && count($this->model->getItems()) > 1000)) {
            $max = ($format != 'google-news') ? 50000 : 1000;

            // check if limiting size of items array is enabled
            if (! $this->model->getUseLimitSize()) {
                // use sitemapindex and generate partial sitemaps
                foreach (array_chunk($this->model->getItems(), $max) as $key => $item) {
                    // reset current items
                    $this->model->resetItems($item);

                    // generate new partial sitemap
                    $this->store($format, $filename.'-'.$key, $path, $style);

                    // add sitemap to sitemapindex
                    if ($path != null) {
                        // if using custom path generate relative urls for sitemaps in the sitemapindex
                        $this->addSitemap($filename.'-'.$key.'.'.$fe);
                    } else {
                        // else generate full urls based on app's domain
                        $this->addSitemap(url($filename.'-'.$key.'.'.$fe));
                    }
                }

                $data = $this->generate('sitemapindex', $style);
            } else {
                // reset items and use only most recent $max items
                $this->model->limitSize($max);
                $data = $this->generate($format, $style);
            }
        } else {
            $data = $this->generate($format, $style);
        }

        // clear memory
        if ($format == 'sitemapindex') {
            $this->model->resetSitemaps();
        }

        $this->model->resetItems();

        // if custom path
        if ($path == null) {
            $file = public_path().DIRECTORY_SEPARATOR.$filename.'.'.$fe;
        } else {
            $file = $path.DIRECTORY_SEPARATOR.$filename.'.'.$fe;
        }

        if ($this->model->getUseGzip() == true) {
            // write file (gzip compressed)
            $this->file->put($file, gzencode((string) $data['content'], 9));
        } else {
            // write file
            $this->file->put($file, $data['content']);
        }
    }
}
