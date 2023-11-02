
<?php

/* Warehouse Transaction group */

$router->group(['prefix' => 'transactions', 'as' => 'price_codes'], function () use ($router) {
  /* All Warehouse can add request param status=0 or 1*/
  $router->get('/', ['as' => 'all', 'uses' => 'WMS\TransactionController@index']);

  // /* Show Warehouse by uuid can add request param status=0 or 1*/
  // $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'WMS\TransactionController@show']);

  /* create Warehouse */
  $router->post('/', ['as' => 'create', 'uses' => 'WMS\TransactionController@store']);

  // /* Update Bulk Warehouse by uuid */
  // $router->put('/', ['as' => 'update', 'uses' => 'WMS\TransactionController@updateBulk']);

  // /* Bulk delete Warehouse */
  // $router->delete('/', ['as' => 'delete', 'uses' => 'WMS\TransactionController@destroyBulk']);
});
