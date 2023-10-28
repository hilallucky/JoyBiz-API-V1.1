<?php

$router->group(['prefix' => 'bonus', 'as' => 'bonus'], function () use ($router) {

    $router->post('/weekly', ['as' => 'all', 'uses' => 'Calculations\Bonuses\PeriodController@generateWeekPeriods']);
    $router->get('/voucher/{member_uuid}', ['as' => 'get data voucher', 'uses' => 'Calculations\Bonuses\VoucherController@getByMember']);
});
