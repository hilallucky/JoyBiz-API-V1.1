<?php
use App\Http\Controllers\ProductAttributeController;

/* Price Code group */
$router->group(['prefix' => 'product-attributes', 'as' => 'product_attribute'], function () use ($router) {

    /* restrict route */
    // $router->group(['middleware' => ['client', 'auth']], function () use ($router) {

    /* All Price Code can add request param status=0 or 1*/
    $router->get('/', ['as' => 'all', 'uses' => 'Products\ProductAttributeController@index']);

    /* Show Price Code by uuid can add request param status=0 or 1*/
    $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Products\ProductAttributeController@show']);

    /* create Price Code */
    $router->post('/', ['as' => 'create', 'uses' => 'Products\ProductAttributeController@store']);

    /* Update Bulk Price Code by uuid */
    $router->put('/', ['as' => 'update', 'uses' => 'Products\ProductAttributeController@updateBulk']);

    /* Bulk delete Price Code */
    $router->delete('/delete', ['as' => 'show', 'uses' => 'Products\ProductAttributeController@destroyBulk']);


    // });
});
