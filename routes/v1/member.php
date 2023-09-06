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

// /* getUplineGenealogy */
// $router->get('/members/{uuid}/genealogy/upline', [
//     'as' => 'member-getUplineGenealogy',
//     'uses' => 'Members\MemberController@getUplineGenealogy'
// ]);
