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
    
    public function operations()
    {
        return [
            'hello' => [
                'description' => 'Simple hello handshake on the iCare website.',
                'controller' => 'ICareWebPageScraper\\Http\\Controllers\\ICareWebPageScraperPluginHelloController@hello',
                'requires' => [],
                'parameters' => [],
            ],
            'item/{id}' => [
                'description' => 'Scrape an service item from the iCare website.',
                'controller' => 'ICareWebPageScraper\\Http\\Controllers\\ICareWebPageScraperPluginController@retrieve',
                'requires' => [
                    'id',
                ],
                'parameters' => [
                    'path' => [
                        'id' => [
                            'type' => 'string',
                            'description' => 'iCare service item ID',
                            'example' => 'ydk_nBCWDI0',
                        ]
                    ],
                    'query' =>[
                        'selector' => [
                            'type' => 'array',
                            'description' => 'CSS selector',
                            'example' => 'selector[]=.service_contact dd:nth-child(2)&selector[]=.service_contact dd:nth-child(4)&selector[]=dd:nth-child(6) > a',
                        ]
                    ],
                ],
            ],
            'hello/create' => [
                'description' => 'Create a hello handshake request for the iCare website.',
                'controller' => 'ICareWebPageScraper\\Http\\Controllers\\ICareWebPageScraperPluginHelloController@create',
                'requires' => [],
                'parameters' => [],
            ],
        ];
    }
}