<?php

use App\Http\Controllers\Orders\OrderController;

/* Order group */

$router->group(['prefix' => 'orders', 'as' => 'orders'], function () use ($router) {
    /* All Approved Order can add request param status=0 or 1*/
    $router->get('/success', ['as' => 'all', 'uses' => 'Orders\OrderController@getOrderApprovedList']);

    /* Show Approved Order by uuid can add request param status=0 or 1*/
    $router->get('/success/{uuid}/details', ['as' => 'show', 'uses' => 'Orders\OrderController@getOrderApprovedDetails']);

    /* Bulk delete Approved Order */
    $router->delete('/success', ['as' => 'delete', 'uses' => 'Orders\OrderController@destroyApprovedBulk']);

    /* All Temporary Order can add request param status=0 or 1*/
    $router->get('/temp', ['as' => 'all', 'uses' => 'Orders\OrderController@getOrderTempList']);

    /* Show Temporary Order by uuid can add request param status=0 or 1*/
    $router->get('/temp/{uuid}/details', ['as' => 'show', 'uses' => 'Orders\OrderController@getOrderTempDetails']);

    /* create Order */
    $router->post('/', ['as' => 'create', 'uses' => 'Orders\OrderController@store']);

    // /* Update Bulk Order by uuid */
    // $router->put('/', ['as' => 'update', 'uses' => 'Orders\OrderController@updateBulk']);

    /* Bulk delete Temporary Order */
    $router->delete('/temp', ['as' => 'delete', 'uses' => 'Orders\OrderController@destroyTempBulk']);

    /* Approve Bulk Order by uuids */
    $router->post('/approve', ['as' => 'approve', 'uses' => 'Orders\OrderController@approveOrder']);
});
