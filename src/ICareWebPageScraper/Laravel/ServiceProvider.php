<?php

namespace ICareWebPageScraper\Laravel;

/**
 * Class ServiceProvider
 *
 * @package ICareWebPageScraper\Laravel
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
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
    public $version = '0.1';

    /**
     * Implementation of boot method.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '../routes.php');
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