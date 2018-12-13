<?php

namespace ICareWebPageScraper;

use App\Plugins\WebPageScraper\AbstractWebPageScraperServiceProvider;
use App\Plugins\WebPageScraper\Scraper\WebPageScraperInterface;

/**
 * Class ServiceProvider
 *
 * @package ICareWebPageScraper\Laravel
 */
class ICareWebPageScraperServiceProvider extends AbstractWebPageScraperServiceProvider implements WebPageScraperInterface
{
    /**
     * Scraper package name.
     *
     * @var string
     */
    public $name = 'icare_webpage_scraper_package';

    /**
     * Scraper package description.
     *
     * @var string
     */
    public $description = 'iCare webpage scraper package';

    /**
     * Scraper package version.
     *
     * @var string
     */
    public $version = '0.1.4';

    /**
     * Implementation of boot method.
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Implementation of register method.
     *
     * @return void
     */
    public function register()
    {
        // Needed for Laravel < 5.3 compatibility
    }
}