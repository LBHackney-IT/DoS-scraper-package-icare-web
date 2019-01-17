<?php
/** @var \Laravel\Lumen\Routing\Router $router */
$router->group(['prefix' => 'scraper'], function () use ($router) {
    $router->group(['prefix' => 'web'], function () use ($router) {
        $router->group(['prefix' => 'icare'], function () use ($router) {
            $router->get('item/{id}', ['uses' => 'ICareWebPageScraperPluginController@retrieve']);
            $router->get('item/create/{id}', ['uses' => 'ICareWebPageScraperPluginController@create']);
            $router->get('hello', ['uses' => 'ICareWebPageScraperPluginHelloController@hello']);
            $router->get('hello/create', ['uses' => 'ICareWebPageScraperPluginHelloController@create']);
        });
    });
});
