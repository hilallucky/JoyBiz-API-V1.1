<?php

use App\Http\Controllers\ProductAttributeController;

/* Product Attribute group */

$router->group(['prefix' => 'attributes', 'as' => 'attribute'], function () use ($router) {
    /* All Product Attribute can add request param status=0 or 1*/
    $router->get('/', ['as' => 'all', 'uses' => 'Products\ProductAttributeController@index']);

    /* Show Product Attribute by uuid can add request param status=0 or 1*/
    $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Products\ProductAttributeController@show']);

    /* create Product Attribute */
    $router->post('/', ['as' => 'create', 'uses' => 'Products\ProductAttributeController@store']);

    /* Update Bulk Product Attribute by uuid */
    $router->put('/', ['as' => 'update', 'uses' => 'Products\ProductAttributeController@updateBulk']);

    /* Bulk delete Product Attribute */
    $router->delete('/', ['as' => 'delete', 'uses' => 'Products\ProductAttributeController@destroyBulk']);
});
