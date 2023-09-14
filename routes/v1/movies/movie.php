<?php

/* movie group */
$router->group(['prefix' => 'movie', 'as' => 'movie'], function () use ($router) {

    /* all movies */
    $router->get('/all', ['as' => 'all', 'uses' => 'MovieController@all']);

    /* show movies by uuid */
    $router->get('/{uuid}', ['as' => 'show', 'uses' => 'MovieController@show']);

    /* restrict route */
    $router->group(['middleware' => ['client','auth']], function () use ($router) {
        // $router->group(['middleware' => ['auth']], function () use ($router) {

            /* movies viewed */
            $router->put('/{uuid}/viewed', ['as' => 'viewed', 'uses' => 'MovieController@viewed']);

            /* create movies */
            $router->post('/create', ['as' => 'create', 'uses' => 'MovieController@create']);

            /* update movies */
            $router->patch('/{uuid}/update', ['as' => 'update', 'uses' => 'MovieController@update']);

            /* delete movies */
            $router->delete('/{uuid}/delete', ['as' => 'delete', 'uses' => 'MovieController@delete']);

            $router->get('/{uuid}/show', ['as' => 'show-admin', 'uses' => 'MovieController@show']);
        });
    // });

});
