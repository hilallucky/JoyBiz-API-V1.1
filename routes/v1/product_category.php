<?php
use App\Http\Controllers\Products\ProductCategoryController;

/* Product Categories group */
$router->group(['prefix' => 'categories', 'as' => 'product-categories'], function () use ($router) {

    /* restrict route */
    // $router->group(['middleware' => ['client', 'auth']], function () use ($router) {

    /* All Product Categories can add request param status=0 or 1*/
    $router->get('/', ['as' => 'all', 'uses' => 'Products\ProductCategoryController@index']);

    /* Show Product Categories by uuid can add request param status=0 or 1*/
    $router->get('/{uuid}', ['as' => 'show', 'uses' => 'Products\ProductCategoryController@show']);

    /* Update Product Categories by uuid */
    $router->put('/{uuid}', ['as' => 'update', 'uses' => 'Products\ProductCategoryController@update']);

    /* Update Bulk Product Categories by uuid */
    $router->put('/', ['as' => 'update', 'uses' => 'Products\ProductCategoryController@updateBulk']);

    /* create Product Categories */
    $router->post('/', ['as' => 'create', 'uses' => 'Products\ProductCategoryController@store']);

    /* Single delete Product Categories */
    $router->delete('/{uuid}/delete', ['as' => 'delete', 'uses' => 'Products\ProductCategoryController@destroy']);

    /* Bulk delete Product Categories */
    $router->delete('/delete', ['as' => 'show', 'uses' => 'Products\ProductCategoryController@destroyBulk']);


    // });
});
