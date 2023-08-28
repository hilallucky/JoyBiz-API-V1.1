<?php
use App\Http\Controllers\PriceCodeController;

/* Price Code group */
$router->group(['prefix' => 'price-codes', 'as' => 'price_codes'], function () use ($router) {

    /* restrict route */
    // $router->group(['middleware' => ['client', 'auth']], function () use ($router) {

    /* All Price Code can add request param status=0 or 1*/
    $router->get('/', ['as' => 'all', 'uses' => 'Products\PriceCodeController@index']);

    /* Show Price Code by uuid can add request param status=0 or 1*/
    $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Products\PriceCodeController@show']);

    /* create Price Code */
    $router->post('/', ['as' => 'create', 'uses' => 'Products\PriceCodeController@store']);

    /* Update Bulk Price Code by uuid */
    $router->put('/', ['as' => 'update', 'uses' => 'Products\PriceCodeController@updateBulk']);

    /* Bulk delete Price Code */
    $router->delete('/delete', ['as' => 'show', 'uses' => 'Products\PriceCodeController@destroyBulk']);


    // });
});
