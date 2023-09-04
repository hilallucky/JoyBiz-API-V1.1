<?php
use App\Http\Controllers\Utils\GalleryController;

/* Product group */
$router->group(['prefix' => 'files', 'as' => 'uploads'], function () use ($router) {

    /* restrict route */
    // $router->group(['middleware' => ['client', 'auth']], function () use ($router) {

    /* All Product Files can add request param status=0 or 1*/
    $router->get('/', ['as' => 'all', 'uses' => 'Utils\GalleryController@index']);

    /* Show Product Files by uuid can add request param status=0 or 1*/
    $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Utils\GalleryController@show']);

    /* create Product Files */
    $router->post('/new', ['as' => 'upload-filed', 'uses' => 'Utils\GalleryController@store']);

    /* Update Bulk Product Files by uuid */
    $router->put('/', ['as' => 'update', 'uses' => 'Utils\GalleryController@updateBulk']);

    /* Bulk delete Product Files */
    $router->delete('/delete', ['as' => 'show', 'uses' => 'Utils\GalleryController@destroyBulk']);

    // });

});