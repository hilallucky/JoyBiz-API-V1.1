
<?php

/* Warehouse Transaction group */

$router->group(['prefix' => 'stocks', 'as' => 'stocks'], function () use ($router) {
  $router->post('/period', ['as' => 'period', 'uses' => 'WMS\StockController@generatePeriod']);
  $router->get('/period/active', ['as' => 'period.active.all', 'uses' => 'WMS\StockController@getActivePeriod']);
  $router->get('/period/active/{date}/{type}', ['as' => 'period.active', 'uses' => 'WMS\StockController@getActivePeriod']);
  $router->post('/', ['as' => 'create', 'uses' => 'WMS\TransactionController@store']);
});
