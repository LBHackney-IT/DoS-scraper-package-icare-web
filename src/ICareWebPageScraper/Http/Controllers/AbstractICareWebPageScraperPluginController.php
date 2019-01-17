<?php

namespace ICareWebPageScraper\Http\Controllers;

use App\Plugins\WebPageScraper\Http\Controllers\AbstractWebPageScraperController;
use ICareWebPageScraper\Http\Driver\ICareWebPageHttpDriver;
use ICareWebPageScraper\Http\ICareWebPageHttpService;
use Illuminate\Http\Request;

/**
 * Class ICareWebPageScraperPluginController
 *
 * @package App\Scraper\ICareWebPageScraper\Http\Controllers
 */
abstract class AbstractICareWebPageScraperPluginController extends AbstractWebPageScraperController
{
    /**
     * @var string
     */
    protected $baseUrl = 'https://www.hackneyicare.org.uk';

    /**
     * @var \App\Scraper\ICareWebPageScraper\Http\ICareWebPageHttpService
     */
    protected $service;

    /**
     * {@inheritdoc}
     */
    protected function makeService()
    {
        $this->conf = array_merge(['base_url' => $this->baseUrl, 'path' => $this->path], $this->conf);
        $driver = new ICareWebPageHttpDriver($this->conf);
        $this->service = new ICareWebPageHttpService($driver, $this->conf);
    }
}
