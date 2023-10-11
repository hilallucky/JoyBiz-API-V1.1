<?php

/* registration */
$router->post('/register', [
    'as' => 'register',
    'uses' => 'Users\AuthController@register'
]);

/* login */
$router->post('/login', [
    'as' => 'login',
    'uses' => 'Users\AuthController@login'
]);

/* restrict route */
$router->group(['middleware' => 'auth'], function () use ($router) {

    /* get user profile */
    $router->get('/profile', [
        'as' => 'profile',
        'uses' => 'Users\AuthController@profile'
    ]);

    /* logout user */
    $router->get('/logout', [
        'as' => 'logout',
        'uses' => 'Users\AuthController@logout'
    ]);

    /* refresh token */
    $router->get('/refresh-token', [
        'as' => 'refreshToken',
        'uses' => 'Users\AuthController@refresh'
    ]);


});
