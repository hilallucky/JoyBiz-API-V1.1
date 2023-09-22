<?php

/* Point Calculation group */

$router->group(['prefix' => 'point', 'as' => 'point'], function () use ($router) {
    
    $router->post('/pv/1', ['as' => 'all', 'uses' => 'Calculation\PointCalculationController@getMlmData']);
    $router->post('/pv/2', ['as' => 'all', 'uses' => 'Calculation\PointCalculationController@calculationPoint']);
    $router->post('/pv/3', ['as' => 'all', 'uses' => 'Calculation\PointCalculationController@getMlmData_V2']);
    
});
