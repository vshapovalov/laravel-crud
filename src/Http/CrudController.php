<?php

namespace Vshapovalov\Crud\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Vshapovalov\Crud\Facades\Crud;


class CrudController extends BaseController
{

	public function __construct() {

		$this->middleware('auth');
	}

	function getAdminIndex(){

		if (Auth::check()) {

			return View::make('crud::index');
		} else {

			return redirect('login');
		}
	}

	function getCrudList(){

		return Crud::getCrudList();
	}

	function getCrudConfig(){

		return Crud::getCrudConfig();
	}

	function getMenuList(){

		return Crud::getMenuList();
	}

	function getItemsListTest($code, Request $request){
		return [
			'items' => Crud::getCrudItemsList($code, [], [ 'field' => ''])
		];
	}

	function getItemsList($code, Request $request){

		return [
			'items' => Crud::getCrudItemsList(
				$code,
				$request->input('item', []),
				$request->input('sort', [ 'field' => '']))
		];

	}

	function getItem($code, $id){

		return Crud::getModelByCrud($code, $id, true);

	}

	function updateItem($code, Request $request){
		$response = [
			'status' => 'success'
		];

		if ($errors = Crud::saveCrudItem($code, $request->input('item'))){
			$response['status'] = 'error';
			$response['errors'] = $errors;
		}

		return $response;
	}

	function deleteItem($code, $id){

		$response = [
			'status' => 'success'
		];

		Crud::deleteCrudItem($code, $id);

		return $response;

	}

	function treeUpdateItems($code, Request $request){

		$response = [
			'status' => 'success'
		];

		if($data = $request->input('data', false)) {

			foreach ($data as $treeItem){

				Crud::saveCrudItem($code, $treeItem);
			}

		}

		return response( $response, 200);

	}
}
