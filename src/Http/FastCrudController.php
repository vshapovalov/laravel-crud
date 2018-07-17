<?php

namespace Vshapovalov\Crud\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Vshapovalov\Crud\Facades\Crud;
use Vshapovalov\Crud\Models\CrudForm;


class FastCrudController extends BaseController
{
	public function __construct() {

		$this->middleware('auth');
	}

	function getTables(){

		return Crud::isUserAdmin() ? Crud::getTables() : [];
	}

	function getForms(){

		return Crud::isUserAdmin() ? CrudForm::with('fields', 'scopes')->get() : [];
	}

	function addForm(){

		if (Crud::isUserAdmin()) {

			$form = request()->get( 'form' );

			Crud::createForm( request()->get( 'form' ) );

			if ( request()->get('createModel', false)) {
				Crud::createModelFromDummy( $form['model'] );
			}

		}

		return [ 'status' => 'ok' ];
	}
}