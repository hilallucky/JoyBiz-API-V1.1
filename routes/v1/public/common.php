<?php

/* test response */
$router->get('/ping', ['as' => 'ping', function () use ($router) {
  return 'pong';
}]);

/* lumen version */
$router->get('/version', ['as' => 'version', function () use ($router) {
  return $router->app->version();
}]);

/* test redis */
$router->get('/test-redis', ['as' => 'test-redis', 'uses' => 'RedisTestController@testConnection']);

$router->get('/phpinfo', ['as' => 'phpinfo', 'uses' => 'ExampleController@index']);
