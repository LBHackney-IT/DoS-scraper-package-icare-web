<?php

namespace ICareWebPageScraper;

use ReflectionClass;
use ReflectionException;

/**
 * Class ServiceProvider
 *
 * @package ICareWebPageScraper\Laravel
 */
class ICareWebPageScraperServiceProvider extends \Illuminate\Support\ServiceProvider
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
     * @var $this
     */
    private $reflector = null;

    /**
     * Implementation of boot method.
     *
     * @return void
     */
    public function boot()
    {
        $this->enableRoutes('routes.php');
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

    /**
     * Enable routes for this plugin.
     *
     * @param string $path
     */
    protected function enableRoutes($path = 'routes.php')
    {
        $this->app->router->group(
            ['namespace' => $this->getPluginControllerNamespace()],
            function ($router) use ($path) {
                require __DIR__ . DIRECTORY_SEPARATOR . $path;
            }
        );
    }


    /**
     * Get the plugin controller namespace.
     *
     * @return string
     */
    protected function getPluginControllerNamespace()
    {
        try {
            $reflector = $this->getReflector();
            $baseDir = str_replace($reflector->getShortName(), '', $reflector->getName());

            return $baseDir . 'Http\\Controllers';
        } catch (ReflectionException $e) {
            dd('Plugin namespace could not be determined: "' . $e->getMessage() . '"');
            exit;
        }
    }

    /**
     * Get the class reflector.
     *
     * @return \ReflectionClass
     *
     * @throws \ReflectionException
     */
    private function getReflector()
    {
        if (is_null($this->reflector)) {
            $this->reflector = new ReflectionClass($this);
        }

        return $this->reflector;
    }
}