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
     * Implementation of boot method.
     *
     * @return void
     */
    public function boot()
    {
        // Do something here
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