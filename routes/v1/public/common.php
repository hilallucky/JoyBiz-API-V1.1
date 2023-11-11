<?php

/* test response */

use Illuminate\Support\Facades\Hash;

$router->get('/ping', ['as' => 'ping', function () use ($router) {
  return 'pong';
}]);

/* Hash password */
$router->get('/hash/{password}', ['as' => 'ping', function (string $password) use ($router) {
  return Hash::make($password);
}]);

/* lumen version */
$router->get('/version', ['as' => 'version', function () use ($router) {
  return $router->app->version();
}]);

/* test redis */
$router->get('/test-redis', ['as' => 'test-redis', 'uses' => 'RedisTestController@testConnection']);

$router->get('/phpinfo', ['as' => 'phpinfo', 'uses' => 'ExampleController@index']);
