<?php

/* registration */
$router->post('/members', [
    'as' => 'member-register',
    'uses' => 'Members\MemberController@register'
]);

/* getGenealogy */
$router->get('/members/{uuid}/genealogy/{type}', [
    'as' => 'member-getGenealogy',
    'uses' => 'Members\MemberController@getGenealogy'
]);

/* getUplineGenealogy */
$router->get('/members/{uuid}/upline/{type}', [
    'as' => 'member-getUpline',
    'uses' => 'Members\MemberController@getUpline'
]);

/* get Member List */
$router->get('/members/list', [
    'as' => 'member-list',
    'uses' => 'Members\MemberController@getMemberList'
]);

/* check Network */
$router->get('/members/check-network', [
    'as' => 'member-checkNetwork',
    'uses' => 'Members\MemberController@checkNetwork'
]);
