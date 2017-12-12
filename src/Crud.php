<?php

namespace Vshapovalov\Crud;

use Illuminate\Support\Facades\Validator;
use Vshapovalov\Crud\Models\AdminSetting;
use Vshapovalov\Crud\Models\MenuItem;

class Crud {

	protected $config = [];
	protected $cruds = [];
	protected $menu = [];

	protected $settings = null;
	protected $menuItems = null;

	function __construct() {
		$this->config = config('cruds');

		foreach($this->config['list'] as &$crud){
			$this->cruds[$crud['code']] = $crud;
		}

		$this->menu = $this->config['menu'];
	}

	public function routes()
	{
		require __DIR__.'/../routes/Routes.php';
	}

	function loadSettings(){

		$this->settings = AdminSetting::with('adminSettingGroup')->get()->toArray();

	}

	function loadMenuItems() {
		$this->menuItems = MenuItem::with( 'children' )->get();
	}

	function setDefaultValues(){
		foreach ($this->config['list'] as &$crud){
			foreach($crud['meta']['fields'] as &$field){
				if (isset($field['by_default']) && is_callable($field['by_default'])){
					$field['by_default'] = $field['by_default']();
					debug($field['by_default']);
				}
			}
		}
	}

	function getCrudList(){
		return $this->cruds;
	}

	function getCrudConfig(){
		$this->setDefaultValues();
		return $this->config;
	}

	function getMenuList(){
		return $this->config['menu'];
	}

	function getCrudByCode($code){
		return $this->cruds[$code];
	}

	function getModelByCrud($crud, $id, $withRelations){
		if (is_string($crud))
			$crud = $this->getCrudByCode($crud);

		$item = new $crud['model'];

		$qb = call_user_func($crud['model']."::query");

		if ($withRelations) {

			$relationships = array_keys($item->relationships());

			$qb->with($relationships);
		}

		return $qb->find($id);
	}

	function image($items, $isArray = false){

		if ($items) {

			$values = json_decode($items);

			if ($isArray){

				return array_map(function($i){return url($i);}, $values);
			} else {

				return isset($values[0]) ? url($values[0]) : '';
			}

		} else {
			if ( $isArray ) {

				return [];
			} else {

				return '';
			}

		}
	}

	function imageThumb($image, $thumbName){

		$ext = substr($image, strrpos($image, '.'));

		$firstPartOfName = substr($image, 0, strrpos($image, '.'));

		return $firstPartOfName . '-' . $thumbName . $ext;

	}

	function settings($settingCode){

		if (!$this->settings) $this->loadSettings();

		try{

			[$group, $code] = explode('.', $settingCode);

			return array_first($this->settings, function($settingCode, $key) use ($group, $code){

				return $settingCode['key'] == $code && $settingCode['admin_setting_group']['code'] == $group;
			})['value'];
		} catch (\Exception $e){
			return '';
		}
	}

	function menu($code, $viewName = null){

		if (!$this->menuItems) $this->loadMenuItems();

		if (is_string($code)){
			$searchField = 'code';
		} else {
			$searchField = 'id';
		}

		$menu = array_first($this->menuItems, function($value) use ($code, $searchField){
			return $value->{$searchField} == $code;
		});

		return view($viewName ? $viewName : 'crud::menu', ['menuItem' => $menu])->render();
	}

	function getCrudItemsList($crud, $inputItem, $sortOptions){

		if (is_string($crud))
			$crud = $this->getCrudByCode($crud);

		$item = new $crud['model'];

		$qb = call_user_func($crud['model']."::query");

		$relationships = array_keys($item->relationships());

//		$fields = array_map(function($f){
//			return $f['name'];
//		},
//			array_filter($crud['meta']['fields'], function($f){ return $f['type'] != 'relation'; })
//		);
//
//		$qb->select($fields);

		$relationFields = array_map(function($f){
			return $f['relation']['name'];
		}, array_filter($crud['meta']['fields'], function($f){
			return $f['type'] == 'relation' && (!isset($f['json']) || (isset($f['json']) && !$f['json']));
		}));

		$relationFields = array_values($relationFields);

		if (count($relationFields)){
			$qb = $qb->with($relationFields);
		}

		if (isset($crud['scopes']) && count($crud['scopes'])){

			forEach($crud['scopes'] as $scope){

				if (isset($scope['name']) && !empty($scope['name']))
				{
					$inputParams = [];

					if (isset($scope['params']) && count($scope['params'])){

						if (empty($inputItem)) continue;

						forEach($scope['params'] as $param){

							$inputParams[] = $inputItem[$param];
						}

						$qb = $qb->{$scope['name']}(...$inputParams);
					} else {

						$qb = $qb->{$scope['name']}();
					}

				}
			}
		}

		if ($sortOptions['field'] <> ''){
			$qb->orderBy($sortOptions['field'], $sortOptions['type']);
		}

		return $qb->get();
	}

	function deleteCrudItem($crud, $id){
		if (is_string($crud))
			$crud = $this->cruds[$crud];

		if (!isset($crud['type']) || $crud['type'] == 'list') {
			call_user_func($crud['model']."::whereKey", $id)->delete();
		}

		if (isset($crud['type']) && $crud['type'] == 'tree'){

			call_user_func($crud['model']."::where", 'path', 'like',
				'%'.str_pad($id, 4, '0', STR_PAD_LEFT) . ';%')->delete();
		}

	}

	/**
	 *
	 *
	 */
	function validateValuesByCrud($crud, $inputValues){

		$validatingFields = array_filter($crud['meta']['fields'], function($field){
			return isset($field['validation']);
		});

		$validationRules = array_combine(
				array_map( function ($val) {return snake_case($val); }, array_keys($validatingFields)),
				array_map( function ($val) {return $val['validation']; }, $validatingFields)
			);

//		debug($validationRules);

		if (count($validationRules)) {

			$validator = Validator::make($inputValues, $validationRules);

			if ($validator->fails()){
				return $validator->errors();
			}
		}

		return false;
	}



	function saveCrudItem($crud, $inputValues){

		if (is_string($crud))
			$crud = $this->cruds[$crud];


		if ($errors = $this->validateValuesByCrud($crud, $inputValues)){

			return $errors;

		};

		if (isset($inputValues[$crud['id']])) {

			$id = $inputValues[$crud['id']];

			$item = $this->getModelByCrud($crud, $id, true);
		} else {

			$item = new $crud['model'];
		}

		$itemSaved = false;

		foreach ($crud['meta']['fields'] as $field){

			if ($field['type'] == 'dynamic'){

				if ($field['dynamic']['type'] == 'field'){

					$field['type'] = $inputValues[$field['dynamic']['from']];
				}

				if ($field['dynamic']['type'] == 'relation'){

					[$dynamicRelation, $dynamicField] = explode('.', $field['dynamic']['from']);

					$field['type'] = $inputValues[snake_case($dynamicRelation)][$dynamicField];

				}

				if (!isset($id)) continue;
			}

			// if not marked as edit field, then go over
			if (
				!(in_array('edit', $field['visibility'])
				    || in_array('add', $field['visibility'])
					|| (
						!in_array('add', $field['visibility'])
						&& isset($field['by_default'])
					   )
				)
			) {
				continue;
			}

			if ($field['type'] == 'textbox'){

				if (isset($field['additional']) && isset($field['additional']['mode'])
				    && ($field['additional']['mode'] == 'password')) {

					if (isset($inputValues[$field['name']]) && !empty($inputValues[$field['name']])) {

						$item[$field['name']] = bcrypt($inputValues[$field['name']]);
					}
				} else {

					$item[$field['name']] = $inputValues[$field['name']];
				}

				continue;
			}

			if ($field['type'] == 'dropdown'){

				$item[$field['name']] = $inputValues[$field['name']];

				continue;
			}

			if ($field['type'] == 'checkbox'){

				$item[$field['name']] = $inputValues[$field['name']];

				continue;
			}

			if ($field['type'] == 'datepicker'){

				$item[$field['name']] = $inputValues[$field['name']];

				continue;
			}

			if ($field['type'] == 'colorbox'){

				$item[$field['name']] = $inputValues[$field['name']];

				continue;
			}

			if ($field['type'] == 'image'){

				$item[$field['name']] = $inputValues[$field['name']];

				continue;
			}

			if ($field['type'] == 'textarea'){

				$item[$field['name']] = $inputValues[$field['name']];

				continue;
			}

			if ($field['type'] == 'richedit'){

				$item[$field['name']] = $inputValues[$field['name']];

				continue;
			}

			if ( $field['type'] == 'relation' && isset($field['json']) && $field['json'] ) {

				$item[$field['name']] = $inputValues[$field['name']];

			}

			if (
				$field['type'] == 'relation' &&
				( !isset($field['json'] ) || ( isset($field['json']) && !$field['json'] ) )
			){

				$crudField = null;

				if (isset($inputValues[snake_case($field['relation']['name'])])) {
					$crudField = $inputValues[snake_case($field['relation']['name'])];
				} else {
					if (isset($crudField)) unset($crudField);
				}

				$relationCrud = $this->getCrudByCode($field['relation']['crud']);

//				debug($field['name']);
//				debug($field['relation']['type']);

				/************************************ belongsTo ************************************/

				if ( $field['relation']['type'] == 'belongsTo' ){

					if (!isset($crudField) || empty($crudField)){
						$item->{$field['relation']['name']}()->dissociate();

						continue;
					}

					$relatedItem = $this->getModelByCrud($relationCrud, $crudField[$relationCrud['id']], false);

					$item->{$field['relation']['name']}()->associate($relatedItem);

					continue;
				}

				/************************************ hasMany ************************************/

				if ($field['relation']['type'] == 'hasMany'){

					if (!$itemSaved) {
						$item->save();
					}


					if (isset($id)){

						$relationShips = $item->relationships();

						$relationForeignKey = $relationShips[$field['relation']['name']]['foreign_key'];

						forEach($item->{$field['relation']['name']} as $relatedItem){
							$relatedItem->{$relationForeignKey} = null;
							$relatedItem->save();
						}

					}

					if (isset($crudField) && !empty($crudField)){
						forEach($crudField as $crudRelatedItem){

							$relatedItem = $this->getModelByCrud($relationCrud, $crudRelatedItem[$relationCrud['id']], true);

							$item->{$field['relation']['name']}()->save($relatedItem);

						}
					}
				}

				/************************************ belongsToMany ************************************/

				if ($field['relation']['type'] == 'belongsToMany'){

					if (!$itemSaved) {
						$item->save();
					}

					$relationIds = [];
					$pivotFields = [];

					if (isset($field['relation']['pivot'])){
						$pivotFields = $field['relation']['pivot']['fields'];
					}

					if (isset($crudField) && !empty($crudField)) {

						forEach ( $crudField as $crudRelatedItem ) {

							if ( isset( $field['relation']['pivot'] ) ) {

								$pivotValues = [];

								forEach ( $field['relation']['pivot']['fields'] as $pivotField ) {
									if (isset($crudRelatedItem['pivot'][ $pivotField['name'] ])) {
										$pivotValues[ $pivotField['name'] ] = $crudRelatedItem['pivot'][ $pivotField['name'] ];
									}
								}

								$relationIds[ $crudRelatedItem[ $relationCrud['id'] ] ] = $pivotValues;
							} else {
								$relationIds[] = $crudRelatedItem[ $relationCrud['id'] ];
							}
						}
					}


					$item->{$field['relation']['name']}()->sync($relationIds);

				}

				/************************************ hasOne ************************************/

				if ($field['relation']['type'] == 'hasOne'){

					if (!$itemSaved) {
						$item->save();
					}

					// TODO: check for empty $crudField and disassociate

					$relatedItem = $this->getModelByCrud($relationCrud, $crudField[$relationCrud['id']], false);

					$item->{$field['relation']['name']}()->save($relatedItem);
				}
			}
		}

		if (!$itemSaved) {
			$item->save();
		}

		if (isset($crud['type']) && $crud['type'] == 'tree'){

			if ($item->parent) {

				$item->level = $item->parent->level + 1;
				$item->path = $item->parent->path . str_pad($item->id, 4, '0', STR_PAD_LEFT) . ';';;

				if(!isset($id) || ($item->parent->id <> $inputValues[ $crud['tree']['parent']])){
					$item->order = call_user_func($crud['model']."::where", $crud['tree']['parent'], '=', $item->parent->id)->
						where($crud['id'],'<>', $item->id)->max('order') + 1;
				} else {
					$item->order = $inputValues['order'];
				}

			} else {
				$item->level =  1;

				$item->order = call_user_func($crud['model']."::whereNull", $crud['tree']['parent'])->
					where($crud['id'],'<>', $item->id)->max('order') + 1;

				$item->path = str_pad($item->id, 4, '0', STR_PAD_LEFT) . ';';;
			}

			$item->save();

		}

		return false;

	}

}