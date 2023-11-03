
<?php

/* Warehouse Transaction group */

$router->group(['prefix' => 'stocks', 'as' => 'stocks'], function () use ($router) {
  $router->post('/period', ['as' => 'period', 'uses' => 'WMS\StockController@generatePeriod']);
  $router->get('/period/active/{date}/{type}', ['as' => 'period.active', 'uses' => 'WMS\StockController@getActivePeriod']);
  // $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'WMS\TransactionController@show']);
  $router->post('/', ['as' => 'create', 'uses' => 'WMS\TransactionController@store']);
  // $router->put('/', ['as' => 'update', 'uses' => 'WMS\TransactionController@updateBulk']);
  // $router->delete('/', ['as' => 'delete', 'uses' => 'WMS\TransactionController@destroyBulk']);
});
