<?php

use App\Http\Controllers\Configs\CountryController;

/* Product group */

$router->group(['prefix' => 'config', 'as' => 'config'], function () use ($router) {

    $router->group(['prefix' => 'countries', 'as' => 'countries'], function () use ($router) {

        /* restrict route */
        // $router->group(['middleware' => ['client', 'auth']], function () use ($router) {

        /* All Country can add request param status=0 or 1*/
        $router->get('/', ['as' => 'all', 'uses' => 'Configs\CountryController@index']);

        /* Show Country by uuid can add request param status=0 or 1*/
        $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Configs\CountryController@show']);

        /* create Country */
        $router->post('/', ['as' => 'create', 'uses' => 'Configs\CountryController@store']);

        /* Update Bulk Country by uuid */
        $router->put('/', ['as' => 'update', 'uses' => 'Configs\CountryController@updateBulk']);

        /* Bulk delete Country */
        $router->delete('/delete', ['as' => 'show', 'uses' => 'Configs\CountryController@destroyBulk']);

        // });
    });

    $router->group(['prefix' => 'cities', 'as' => 'cities'], function () use ($router) {

        /* restrict route */
        // $router->group(['middleware' => ['client', 'auth']], function () use ($router) {

        /* All City can add request param status=0 or 1*/
        $router->get('/', ['as' => 'all', 'uses' => 'Configs\CityController@index']);

        /* Show City by uuid can add request param status=0 or 1*/
        $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Configs\CityController@show']);

        /* create City */
        $router->post('/', ['as' => 'create', 'uses' => 'Configs\CityController@store']);

        /* Update Bulk City by uuid */
        $router->put('/', ['as' => 'update', 'uses' => 'Configs\CityController@updateBulk']);

        /* Bulk delete City */
        $router->delete('/delete', ['as' => 'show', 'uses' => 'Configs\CityController@destroyBulk']);

        // });
    });

    $router->group(['prefix' => 'couriers', 'as' => 'couriers'], function () use ($router) {

        /* restrict route */
        // $router->group(['middleware' => ['client', 'auth']], function () use ($router) {

        /* All Courier can add request param status=0 or 1*/
        $router->get('/', ['as' => 'all', 'uses' => 'Configs\CourierController@index']);

        /* Show Courier by uuid can add request param status=0 or 1*/
        $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Configs\CourierController@show']);

        /* create Courier */
        $router->post('/', ['as' => 'create', 'uses' => 'Configs\CourierController@store']);

        /* Update Bulk Courier by uuid */
        $router->put('/', ['as' => 'update', 'uses' => 'Configs\CourierController@updateBulk']);

        /* Bulk delete Courier */
        $router->delete('/delete', ['as' => 'show', 'uses' => 'Configs\CourierController@destroyBulk']);

        // });
    });

    $router->group(['prefix' => 'payment-types', 'as' => 'payments'], function () use ($router) {

        /* restrict route */
        // $router->group(['middleware' => ['client', 'auth']], function () use ($router) {

        /* All Payment Type can add request param status=0 or 1*/
        $router->get('/', ['as' => 'all', 'uses' => 'Configs\PaymentTypeController@index']);

        /* Show Payment Type by uuid can add request param status=0 or 1*/
        $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Configs\PaymentTypeController@show']);

        /* create Payment Type */
        $router->post('/', ['as' => 'create', 'uses' => 'Configs\PaymentTypeController@store']);

        /* Update Bulk Payment Type by uuid */
        $router->put('/', ['as' => 'update', 'uses' => 'Configs\PaymentTypeController@updateBulk']);

        /* Bulk delete Payment Type */
        $router->delete('/delete', ['as' => 'show', 'uses' => 'Configs\PaymentTypeController@destroyBulk']);

        // });
    });
});
