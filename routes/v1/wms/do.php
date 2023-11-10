
<?php

/* Warehouse Transaction group */

$router->group(['prefix' => 'dos', 'as' => 'do'], function () use ($router) {
  $router->post('/', ['as' => 'create', 'uses' => 'WMS\DOController@store']);
  $router->get('/', ['as' => 'get', 'uses' => 'WMS\DOController@index']);
  $router->get('/test', ['as' => 'test', 'uses' => 'WMS\DOController@test']);
});
