<?php



Route::group(['as' => 'media.', 'prefix' => config('cruds.media_prefix')], function(){

	$namespacePrefix = '\\Vshapovalov\\Crud\\Http\\Controllers\\';

	Route::post('upload', ['as' => 'upload', 'uses' => $namespacePrefix.'MediaController@putMedia']);
	Route::post('items', ['as' => 'items', 'uses' => $namespacePrefix.'MediaController@getItems']);
	Route::post('items/delete', ['as' => 'delete', 'uses' => $namespacePrefix.'MediaController@deleteItem']);
    Route::post('items/move', ['as' => 'move', 'uses' => $namespacePrefix.'MediaController@moveItems']);
	Route::post('folder/new', ['as' => 'newfolder', 'uses' => $namespacePrefix.'MediaController@newFolder']);
	Route::post('folder/rename', ['as' => 'renamefolder', 'uses' => $namespacePrefix.'MediaController@renameFolder']);
    Route::post('crop', ['as' => 'crop', 'uses' => $namespacePrefix.'MediaController@cropImage']);
});

Route::group(['namespace' => '\\Vshapovalov\\Crud\\Http\\Controllers', 'as' => 'cruds.', 'prefix' => config('cruds.crud_prefix')], function(){

	$namespacePrefix = '\\Vshapovalov\\Crud\\Http\\Controllers\\';

	Route::get('/', ['as' => 'admin', 'uses' => $namespacePrefix.'CrudController@getAdminIndex']);
	Route::get('config', ['uses' => $namespacePrefix.'CrudController@getCrudConfig']);
	Route::get('list', ['uses' => $namespacePrefix.'CrudController@getCrudList']);
	Route::get('menu/list', ['uses' => $namespacePrefix.'CrudController@getMenuList']);

	Route::group(['as' => 'extra.', 'prefix' => 'extra'], function() use ($namespacePrefix){
		Route::get('tables', ['uses' => $namespacePrefix.'FastCrudController@getTables']);
		Route::get('forms', ['uses' => $namespacePrefix.'FastCrudController@getForms']);
		Route::post('forms', ['uses' => $namespacePrefix.'FastCrudController@addForm']);
	});

	Route::post('{code}/items', ['uses' => $namespacePrefix.'CrudController@getItemsList']);
	Route::get('{code}/get', ['uses' => $namespacePrefix.'CrudController@getItemsList']);
	Route::get('{code}/{id}', ['uses' => $namespacePrefix.'CrudController@getItem']);
	Route::post('{code}/edit', ['uses' => $namespacePrefix.'CrudController@updateItem']);
	Route::post('{code}/delete/{id}', ['uses' => $namespacePrefix.'CrudController@deleteItem']);
	Route::post('{code}/bulk/update', ['uses' => $namespacePrefix.'CrudController@bulkUpdateItems']);






});