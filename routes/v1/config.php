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
        $router->post('/', ['as' => 'upload-filed', 'uses' => 'Configs\CountryController@store']);

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
        $router->post('/', ['as' => 'upload-filed', 'uses' => 'Configs\CityController@store']);

        /* Update Bulk City by uuid */
        $router->put('/', ['as' => 'update', 'uses' => 'Configs\CityController@updateBulk']);

        /* Bulk delete City */
        $router->delete('/delete', ['as' => 'show', 'uses' => 'Configs\CityController@destroyBulk']);

        // });
    });

    $router->group(['prefix' => 'couriers', 'as' => 'couriers'], function () use ($router) {

        /* restrict route */
        // $router->group(['middleware' => ['client', 'auth']], function () use ($router) {

        /* All City can add request param status=0 or 1*/
        $router->get('/', ['as' => 'all', 'uses' => 'Configs\CourierController@index']);

        /* Show City by uuid can add request param status=0 or 1*/
        $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Configs\CourierController@show']);

        /* create City */
        $router->post('/', ['as' => 'upload-filed', 'uses' => 'Configs\CourierController@store']);

        /* Update Bulk City by uuid */
        $router->put('/', ['as' => 'update', 'uses' => 'Configs\CourierController@updateBulk']);

        /* Bulk delete City */
        $router->delete('/delete', ['as' => 'show', 'uses' => 'Configs\CourierController@destroyBulk']);

        // });
    });

});
