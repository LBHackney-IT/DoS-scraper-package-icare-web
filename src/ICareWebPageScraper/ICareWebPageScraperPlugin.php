<?php

namespace ICareWebPageScraper;

use App\Plugins\WebPageScraper\Scraper\WebPageScraper;

/**
 * Class ICareWebPageScraperPlugin
 *
 * @package App\Scraper\ICareWebPageScraper
 */
class ICareWebPageScraperPlugin extends WebPageScraper
{
    /**
     * @var string
     */
    public $name = 'icare_webpage_scraper_package';

    /**
     * @var string
     */
    public $description = 'iCare webpage scraper package';

    /**
     * @var string
     */
    public $version = '0.1';

    public function boot()
    {
        // TODO: Implement boot() method.
        $this->enableRoutes();
    }
}