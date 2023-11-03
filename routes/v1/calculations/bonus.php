<?php

$router->group(['prefix' => 'bonus', 'as' => 'bonus'], function () use ($router) {
  $router->post('/weekly', ['as' => 'all', 'uses' => 'Calculations\Bonuses\PeriodController@generateWeekPeriods']);
  $router->get('/period/active/{date}', ['as' => 'period.active', 'uses' => 'Calculations\Bonuses\PeriodController@getActivePeriod']);
  $router->get('/period/active', ['as' => 'period.active', 'uses' => 'Calculations\Bonuses\PeriodController@getActivePeriod']);
  $router->get('/voucher/{member_uuid}', ['as' => 'get data voucher', 'uses' => 'Calculations\Bonuses\VoucherController@getByMember']);
  $router->post('/voucher/use', ['as' => 'use voucher', 'uses' => 'Calculations\Bonuses\VoucherController@usedByMember']);
});
