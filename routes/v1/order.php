<?php

use App\Http\Controllers\Orders\OrderController;

/* Order group */

$router->group(['prefix' => 'orders', 'as' => 'orders'], function () use ($router) {
    /* All Order can add request param status=0 or 1*/
    $router->get('/', ['as' => 'all', 'uses' => 'Orders\OrderController@index']);

    /* Show Order by uuid can add request param status=0 or 1*/
    $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Orders\OrderController@show']);

    /* create Order */
    $router->post('/', ['as' => 'create', 'uses' => 'Orders\OrderController@store']);

    /* Update Bulk Order by uuid */
    $router->put('/', ['as' => 'update', 'uses' => 'Orders\OrderController@updateBulk']);

    /* Bulk delete Order */
    $router->delete('/', ['as' => 'delete', 'uses' => 'Orders\OrderController@destroyBulk']);
});
