<?php

use App\Http\Controllers\Utils\GalleryController;

/* Gallery group */

$router->group(['prefix' => 'files', 'as' => 'uploads'], function () use ($router) {
    /* All Gallery Files can add request param status=0 or 1*/
    $router->get('/', ['as' => 'all', 'uses' => 'Utils\GalleryController@index']);

    /* Show Gallery Files by uuid can add request param status=0 or 1*/
    $router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Utils\GalleryController@show']);

    /* create Gallery Files */
    $router->post('/new', ['as' => 'upload-filed', 'uses' => 'Utils\GalleryController@store']);

    /* Update Bulk Gallery Files by uuid */
    $router->put('/', ['as' => 'update', 'uses' => 'Utils\GalleryController@updateBulk']);

    /* Bulk delete Gallery Files */
    $router->delete('/', ['as' => 'delete', 'uses' => 'Utils\GalleryController@destroyBulk']);
});
