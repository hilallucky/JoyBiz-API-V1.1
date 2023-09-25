<?php

use App\Libraries\Core;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/* v1/public group */
$router->group(['prefix' => 'v1/public', 'as' => 'v1'], function () use ($router) {
    Core::renderRoutes('v1/public', $router);
});

/* restrict route */
$router->group(['middleware' => ['client']], function () use ($router) {
    
    /* v1 auth */
    $router->group(['prefix' => 'v1', 'as' => 'v1'], function () use ($router) {
        Core::renderRoutes('v1/auth', $router);
    });

    // $router->group(['middleware' => ['auth']], function () use ($router) {
        /* v1 group */
        $router->group(['prefix' => 'v1', 'as' => 'v1'], function () use ($router) {
            Core::renderRoutes('v1', $router);
        });

        /* v1/warehouses group */
        $router->group(['prefix' => 'v1/warehouses', 'as' => 'v1'], function () use ($router) {
            Core::renderRoutes('v1/warehouses', $router);
        });

        /* v1/products group */
        $router->group(['prefix' => 'v1/products', 'as' => 'v1'], function () use ($router) {
            Core::renderRoutes('v1/products', $router);
        });

        /* v1/calculation */
        $router->group(['prefix' => 'v1/calculations', 'as' => 'v1'], function () use ($router) {
            Core::renderRoutes('v1/calculations', $router);
        });
    // });
});
