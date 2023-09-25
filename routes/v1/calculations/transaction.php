<?php

/* Point Calculation group */

$router->group(['prefix' => 'transaction', 'as' => 'transaction'], function () use ($router) {
    //Temporary
    $router->get('/temp/{start}/{end}', ['as' => 'all-temp', 'uses' => 'Calculations\Transactions\MemberSummaryController@getTransactionTempSummaries']);

    //Production
    $router->get('/{start}/{end}', ['as' => 'all-prod', 'uses' => 'Calculations\Transactions\MemberSummaryController@getTransactionSummaries']);
    $router->post('/{start}/{end}', ['as' => 'all-prod', 'uses' => 'Calculations\Transactions\MemberSummaryController@calculatePoint']);
});
