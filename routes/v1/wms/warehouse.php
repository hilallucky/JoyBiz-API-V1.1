
<?php

/* Warehouse group */

/* All Warehouse can add request param status=0 or 1*/
$router->get('/', ['as' => 'all', 'uses' => 'Warehouses\WarehouseController@index']);

/* Show Warehouse by uuid can add request param status=0 or 1*/
$router->get('/{uuid}/details', ['as' => 'show', 'uses' => 'Warehouses\WarehouseController@show']);

/* create Warehouse */
$router->post('/', ['as' => 'create', 'uses' => 'Warehouses\WarehouseController@store']);

/* Update Bulk Warehouse by uuid */
$router->put('/', ['as' => 'update', 'uses' => 'Warehouses\WarehouseController@updateBulk']);

/* Bulk delete Warehouse */
$router->delete('/', ['as' => 'delete', 'uses' => 'Warehouses\WarehouseController@destroyBulk']);
