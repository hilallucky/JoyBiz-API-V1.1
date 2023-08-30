<?php
use App\Http\Controllers\Products\ProductFileAndImageController;

/* Product group */
$router->group(['prefix' => 'product/upload', 'as' => 'product-uploads'], function () use ($router) {

    /* restrict route */
    // $router->group(['middleware' => ['client', 'auth']], function () use ($router) {

    /* All Product Files can add request param status=0 or 1*/
    $router->get('/', ['as' => 'all', 'uses' => 'Products\ProductFileAndImageController@index']);

    /* Show Product Files by uuid can add request param status=0 or 1*/
    $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Products\ProductFileAndImageController@show']);

    /* create Product Files */
    $router->post('/new', ['as' => 'upload-filed', 'uses' => 'Products\ProductFileAndImageController@store']);

    /* Update Bulk Product Files by uuid */
    $router->put('/', ['as' => 'update', 'uses' => 'Products\ProductFileAndImageController@updateBulk']);

    /* Bulk delete Product Files */
    $router->delete('/delete', ['as' => 'show', 'uses' => 'Products\ProductFileAndImageController@destroyBulk']);

    // });

});
