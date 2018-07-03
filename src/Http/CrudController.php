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

        $response = [
            'status' => 'success'
        ];

        if (Crud::checkAccess($code, 'select')) {
            $response['items'] = Crud::getCrudItemsList(
                $code,
                $request->input('item', []),
                $request->input('sort', [ 'field' => '']));

        } else {

            $response['status'] = 'error';
            $response['errors'] = [
                'access' => ['Недостаточно прав']
            ];

        }

		return $response;

	}

	function getItem($code, $id){

		return Crud::getModelByCrud($code, $id, true);

	}

	function updateItem($code, Request $request){
		$response = [
			'status' => 'success'
		];

		if ($errors = Crud::validateItem($code, $request->input('item'))){
			$response['status'] = 'error';
			$response['errors'] = $errors;
			return $response;

		};

		$crud = Crud::getCrudByCode($code);

		$checkAction = isset($request->input('item')[$crud['id']]) ? 'edit' : 'add';


		if (Crud::checkAccess($code, $checkAction)) {
			$response['item'] = Crud::saveCrudItem( $code, $request->input('item') ) ;

			return $response;
		} else {

			$response['status'] = 'error';
			$response['errors'] = [
				'access' => ['Недостаточно прав']
			];

			return $response;
		}


	}

	function deleteItem($code, $id){

		$response = [
			'status' => 'success'
		];

		if (Crud::checkAccess($code, 'delete')) {
			Crud::deleteCrudItem($code, $id);

			return $response;
		} else {

			$response['status'] = 'error';
			$response['error'] = 'Недостаточно прав';

			return $response;
		}
	}

	function bulkUpdateItems($code ){

		$response = [
			'status' => 'success'
		];

		if($items = request()->get('items', false)) {

			foreach ($items as $item){

				Crud::saveCrudItem($code, $item);
			}

		}

		return response( $response, 200);

	}
}
