<?php

Route::group(['as' => 'media.', 'prefix' => config('cruds.media_prefix')], function(){

	$namespacePrefix = '\\Vshapovalov\\Crud\\Http\\Controllers\\';

	Route::post('upload', ['as' => 'upload', 'uses' => $namespacePrefix.'MediaController@putMedia']);
	Route::post('items', ['as' => 'items', 'uses' => $namespacePrefix.'MediaController@getItems']);
	Route::post('items/delete', ['as' => 'delete', 'uses' => $namespacePrefix.'MediaController@deleteItem']);
	Route::post('folder/new', ['as' => 'newfolder', 'uses' => $namespacePrefix.'MediaController@newFolder']);
	Route::post('folder/rename', ['as' => 'renamefolder', 'uses' => $namespacePrefix.'MediaController@renameFolder']);
});

Route::group(['namespace' => '\\Vshapovalov\\Crud\\Http\\Controllers', 'as' => 'cruds.', 'prefix' => config('cruds.crud_prefix')], function(){

	$namespacePrefix = '\\Vshapovalov\\Crud\\Http\\Controllers\\';

	Route::get('/', ['as' => 'admin', 'uses' => $namespacePrefix.'CrudController@getAdminIndex']);
	Route::get('list', ['uses' => $namespacePrefix.'CrudController@getCrudList']);
	Route::get('{code}/test', ['uses' => $namespacePrefix.'CrudController@getItemsListTest']);
	Route::post('{code}/items', ['uses' => $namespacePrefix.'CrudController@getItemsList']);
	Route::get('{code}/get', ['uses' => $namespacePrefix.'CrudController@getItemsList']);
	Route::get('{code}/{id}', ['uses' => $namespacePrefix.'CrudController@getItem']);
	Route::post('{code}/edit', ['uses' => $namespacePrefix.'CrudController@updateItem']);
	Route::post('{code}/delete/{id}', ['uses' => $namespacePrefix.'CrudController@deleteItem']);
	Route::post('{code}/tree/move', ['uses' => $namespacePrefix.'CrudController@treeUpdateItems']);
});