<?php

$router->group(['prefix' => 'bonus', 'as' => 'bonus'], function () use ($router) {

    $router->post('/weekly', ['as' => 'all', 'uses' => 'Calculations\Bonus\PeriodController@generateWeekPeriods']);
});
