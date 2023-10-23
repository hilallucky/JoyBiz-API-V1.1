<?php

/* Point Calculation group */

$router->group(['prefix' => 'transaction', 'as' => 'transaction'], function () use ($router) {
    //Temporary
    $router->get('/temp/{start}/{end}', ['as' => 'all-temp', 'uses' => 'Calculations\Transactions\MemberSummaryController@getTransactionTempSummaries']);

    //Production
    $router->get('/{start}/{end}', ['as' => 'all-prod', 'uses' => 'Calculations\Transactions\MemberSummaryController@getTransactionSummaries']);
    $router->post('/{start}/{end}', ['as' => 'all-prod', 'uses' => 'Calculations\Transactions\MemberSummaryController@calculatePointFromTransactions']);
    $router->post('/test/{start}/{end}', ['as' => 'all-prod', 'uses' => 'Calculations\Transactions\MemberSummaryController@updateAccumulatedPointsForAllMembers']);
    $router->post('/get-pv/{start}/{end}', ['as' => 'all-prod', 'uses' => 'Calculations\Transactions\MemberSummaryController@getAccumulatedPoints']);
    
});

$router->post('/calc/test/test/{sponsorUuid}', ['as' => 'calc-prod', 'uses' => 'CalculatePointsController@calculatePointsRecursively']);
$router->post('/calc/{start}/{end}', ['as' => 'calc-prod', 'uses' => 'CalculatePointsController@calculatePointsRecursively_v2']);
