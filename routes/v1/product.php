<?php
use App\Http\Controllers\Products\ProductController;

/* Product group */
$router->group(['prefix' => 'products', 'as' => 'products'], function () use ($router) {

    /* restrict route */
    // $router->group(['middleware' => ['client', 'auth']], function () use ($router) {

    /* All Product can add request param status=0 or 1*/
    $router->get('/', ['as' => 'all', 'uses' => 'Products\ProductController@index']);

    /* Show Product by uuid can add request param status=0 or 1*/
    $router->get('/{uuid}', ['as' => 'show', 'uses' => 'Products\ProductController@show']);

    /* Update Product by uuid */
    $router->put('/{uuid}', ['as' => 'update', 'uses' => 'Products\ProductController@update']);

    /* Update Bulk Product by uuid */
    $router->put('/', ['as' => 'update', 'uses' => 'Products\ProductController@updateBulk']);

    /* create Product */
    $router->post('/', ['as' => 'create', 'uses' => 'Products\ProductController@store']);

    /* Single delete Product */
    $router->delete('/{uuid}/delete', ['as' => 'delete', 'uses' => 'Products\ProductController@destroy']);

    /* Bulk delete Product */
    $router->delete('/delete', ['as' => 'show', 'uses' => 'Products\ProductController@destroyBulk']);


    // });
});
