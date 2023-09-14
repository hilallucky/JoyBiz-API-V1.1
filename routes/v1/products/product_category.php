<?php

use App\Http\Controllers\Products\ProductCategoryController;

/* Product Categories group */

$router->group(['prefix' => 'categories', 'as' => 'product-categories'], function () use ($router) {
    /* All Product Categories can add request param status=0 or 1*/
    $router->get('/', ['as' => 'all', 'uses' => 'Products\ProductCategoryController@index']);

    /* Show Product Categories by uuid can add request param status=0 or 1*/
    $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Products\ProductCategoryController@show']);

    /* create Product Categories */
    $router->post('/', ['as' => 'create', 'uses' => 'Products\ProductCategoryController@store']);

    /* Update Bulk Product Categories by uuid */
    $router->put('/', ['as' => 'update', 'uses' => 'Products\ProductCategoryController@updateBulk']);

    /* Bulk delete Product Categories */
    $router->delete('/', ['as' => 'delete', 'uses' => 'Products\ProductCategoryController@destroyBulk']);
});
