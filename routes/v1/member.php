<?php

// /* registration */
// $router->post('/members', [
//     'as' => 'member-register',
//     'uses' => 'Members\MemberController@register'
// ]);

// /* getGenealogy */
// $router->get('/members/{uuid}/genealogy/{type}', [
//     'as' => 'member-getGenealogy',
//     'uses' => 'Members\MemberController@getGenealogy'
// ]);

// /* getUplineGenealogy */
// $router->get('/members/{uuid}/upline/{type}', [
//     'as' => 'member-getUpline',
//     'uses' => 'Members\MemberController@getUpline'
// ]);

// /* get Member List */
// $router->get('/members/list', [
//     'as' => 'member-list',
//     'uses' => 'Members\MemberController@getMemberList'
// ]);

// /* check Network */
// $router->get('/members/check-network', [
//     'as' => 'member-checkNetwork',
//     'uses' => 'Members\MemberController@checkNetwork'
// ]);



/* Order group */

$router->group(['prefix' => 'members', 'as' => 'members'], function () use ($router) {
  /* registration */
  $router->post('/', [
    'as' => 'member-register',
    'uses' => 'Members\MemberController@register'
  ]);

  /* getGenealogy */
  $router->get('/{uuid}/genealogy/{type}', [
    'as' => 'member-getGenealogy',
    'uses' => 'Members\MemberController@getGenealogy'
  ]);

  /* getUplineGenealogy */
  $router->get('/{uuid}/upline/{type}', [
    'as' => 'member-getUpline',
    'uses' => 'Members\MemberController@getUpline'
  ]);

  /* get Member List */
  $router->get('/list', [
    'as' => 'member-list',
    'uses' => 'Members\MemberController@getMemberList'
  ]);

  /* check Network */
  $router->get('/check-network', [
    'as' => 'member-checkNetwork',
    'uses' => 'Members\MemberController@checkNetwork'
  ]);


  $router->group(['prefix' => '/shipping-address', 'as' => 'shipping-addresses'], function () use ($router) {
    /* All Member Shipping Address can add request param status=0 or 1*/
    $router->get('/', ['as' => 'all', 'uses' => 'Members\MemberShippingAddressController@index']);

    /* Show Member Shipping Address by uuid can add request param status=0 or 1*/
    $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Members\MemberShippingAddressController@show']);

    /* create Member Shipping Address */
    $router->post('/', ['as' => 'create', 'uses' => 'Members\MemberShippingAddressController@store']);

    /* Update Member Shipping Address by uuid */
    $router->put('/', ['as' => 'update', 'uses' => 'Members\MemberShippingAddressController@update']);

    /* Update Patch Member Shipping Address by uuid */
    $router->patch('/', ['as' => 'patch', 'uses' => 'Members\MemberShippingAddressController@updatePatch']);

    /* Bulk delete Member Shipping Address */
    $router->delete('/', ['as' => 'delete', 'uses' => 'Members\MemberShippingAddressController@destroyBulk']);

  });
});
