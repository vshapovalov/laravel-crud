<?php

namespace Vshapovalov\Crud;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Vshapovalov\Crud\Models\AdminSetting;
use Vshapovalov\Crud\Models\MenuItem;
use Vshapovalov\Crud\Models\CrudForm;
use Vshapovalov\Crud\Models\CrudMenu;
use Vshapovalov\Crud\Models\Role;

class Crud {

	protected $config = [];
	protected $cruds = [];
	protected $menu = [];

	protected $settings = null;
	protected $menuItems = null;

	function __construct() {
		$this->config = config('cruds');
	}

	function loadCrudForms(){
		$cruds = [];

		foreach(CrudForm::with(['fields', 'scopes'])->get()->toArray() as $crud){
			$cruds[$crud['code']] = $crud;
		}

		return $cruds;

	}

	public function routes()
	{
		require __DIR__.'/../routes/Routes.php';
	}

	function loadSettings(){

		$this->settings = AdminSetting::with('adminSettingGroup' )->get()->toArray();
	}

	function loadMenuItems() {
		$this->menuItems = MenuItem::with( ['children' => function($q){
            return $q->orderBy('order');
        }])->orderBy('order')->get();
	}

	function setDefaultValues($cruds){

		foreach ($cruds as &$crud){
			foreach($crud['fields'] as &$field){
				if (isset($field['by_default']) && (str_contains($field['by_default'], 'callback::')) ){

					$struct = explode('::', $field['by_default']);

					if (isset($struct[1]) && function_exists($struct[1])){
						$field['by_default'] = call_user_func($struct[1]);
					}
				}

				$field['visibility'] = json_decode($field['visibility'], true);

				if (isset($field['additional'])) {
					$field['additional'] = json_decode($field['additional'], true);
				}

				if ( $field['type'] == 'relation' && isset($field['relation']['pivot']) && count($field['relation']['pivot'])) {

					foreach($field['relation']['pivot'] as &$pivotfield){
						if (isset($pivotfield['by_default']) && is_callable($pivotfield['by_default'])){
							$pivotfield['by_default'] = $pivotfield['by_default']();
						}

						$pivotfield['visibility'] = json_decode($pivotfield['visibility'], true);

						if (isset($pivotfield['additional'])) {
							$pivotfield['additional'] = json_decode($pivotfield['additional'], true);
						}
					}
				}
			}
		}

		return $cruds;
	}

	function getCrudList(){

		$this->initCrudForms();

		return $this->cruds;
	}

	function initCrudForms(){

		if (!count($this->cruds)){

			$this->cruds = cache()->rememberForever( 'crud.list', function(){
				$cruds = $this->loadCrudForms();

				$cruds = $this->setDefaultValues($cruds);

				return $cruds;
			});

			$this->config['list'] = $this->cruds;
		}
	}

	function loadCrudMenu(){

		$roles = Role::whereHas('users', function($q){
			$q->where('id', '=', Auth::user()->id);
		})->get()->pluck('id');

		$menuItems = CrudMenu::where('status','enabled')->whereHas('roles', function($q) use ($roles){
			$q->whereIn('id', $roles);
		})->orDoesntHave('roles')->get()->toArray();

		$keyedItems = [];

		array_walk($menuItems, function($item) use (&$keyedItems){
			$keyedItems[ $item['id'] ] = $item;
		});

		foreach ($keyedItems as &$item){

			if (!empty($item['parent_id']) && isset($keyedItems[ $item['parent_id'] ])) {
				$keyedItems[ $item['parent_id'] ]['items'][] = $item;
			}
		}

		$menu = [];

		array_walk($keyedItems, function($item) use (&$menu){
			if ( empty($item['parent_id']))
				$menu[] = $item;
		});

		$this->menu = $menu;

		$this->config['menu'] = $menu;
	}

	function getCrudConfig(){

		$this->initCrudForms();

		$this->loadCrudMenu();

		return $this->config;
	}

	function getMenuList(){
		return $this->config['menu'];
	}

	function getCrudByCode($code){
		$this->initCrudForms();

		return $this->cruds[$code];
	}

	function getModelRelations($model){

		$relationships = [];

		foreach((new \ReflectionClass($model))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
		{
			if ($method->class != get_class($model) ||
			    !empty($method->getParameters()) ||
			    $method->getName() == __FUNCTION__) {
				continue;
			}

			try {
				$return = $method->invoke($model);

				if ($return instanceof Relation) {
					$relationships[$method->getName()] = [
						'type' => (new \ReflectionClass($return))->getShortName(),
						'model' => (new \ReflectionClass($return->getRelated()))->getName(),
						'foreign_key' => ((new \ReflectionClass($return))->getShortName() == 'HasMany') ? $return->getExistenceCompareKey() : ''
					];
				}
			} catch(\ErrorException $e) {}
		}

		return $relationships;

	}

	function getModelByCrud($crud, $id, $withRelations){
		if (is_string($crud))
			$crud = $this->getCrudByCode($crud);

		$qb = call_user_func($crud['model']."::query");

		if ($withRelations) {

			$relationFields = array_map(

				function($f){ return $f['name']; },

				array_filter( $crud['fields'], function($f){
					return $f['type'] == 'relation'
					       && ( !$f['json'] )
					       && ( in_array('edit', $f['visibility']) || count($f['visibility']) == 0);
				})
			);

			$relationFields = array_values($relationFields);

			$qb->with($relationFields);
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

	function renderMenu($code, $viewName = null){

        if (!$this->menuItems) $this->loadMenuItems();

        $menu = array_first($this->menuItems, function($value) use ($code){
            return (is_string($code) ? ($value->code == $code) : ($value->id == $code));
        });

        return view($viewName ? $viewName : 'crud::menu', ['menuItem' => $menu])->render();
    }

	function menu($code, $viewName = null){

        if (is_string($code)){
            return Cache::remember('menu.'.$code, 1440, function() use ($viewName, $code){
                return $this->renderMenu($code, $viewName);
            });
        } else {
            return $this->renderMenu($code, $viewName);
        }
	}

	function getCrudItemsList($crud, $inputItem, $sortOptions){

		if (is_string($crud))
			$crud = $this->getCrudByCode($crud);

		$qb = call_user_func($crud['model']."::query");

		$relationFields = array_map(

			function($f){ return $f['name']; },

			array_filter( $crud['fields'], function($f){
				return $f['type'] == 'relation'
				       && ( !$f['json'] )
				       && ( in_array('browse', $f['visibility']) || count($f['visibility']) == 0);
			})
		);

		$relationFields = array_values($relationFields);

		if (count($relationFields)){
			$qb = $qb->with($relationFields);
		}

		if (isset($crud['scopes']) && count($crud['scopes'])){

			forEach($crud['scopes'] as $scope){

				if (isset($scope['name']) && !empty($scope['name']))
				{
					if (isset($scope['params']) && count($scope['params']) && !empty($inputItem)){

						$inputParams = [];

						forEach($scope['params'] as $param){
							if (isset($inputItem[$param['name']]))
								$inputParams[] = $inputItem[$param['name']];
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
            $crud = $this->getCrudByCode($crud);

        $model = $this->getModelByCrud($crud, $id, false);
        $model->delete();

		if (isset($crud['type']) && $crud['type'] == 'tree'){

			call_user_func($crud['model']."::where", 'path', 'like',
				'%'.str_pad($id, 4, '0', STR_PAD_LEFT) . ';%')->delete();
		}

	}

	/**
	 *
	 *
	 */
	function validateItem($crud, $inputValues){

		if (is_string($crud))
			$crud = $this->getCrudByCode($crud);

		$validatingFields = array_filter($crud['fields'], function($field){
			return isset($field['validation']) && !empty($field['validation']);
		});

		$validationRules = array_combine(
			array_map( function ($val) {return snake_case($val); }, array_pluck($validatingFields, 'name')),
			array_map( function ($val) {return $val['validation']; }, $validatingFields)
		);

		$fieldsCaptions = array_pluck($validatingFields, 'caption', 'name');

		if (count($validationRules)) {

			$validator = Validator::make($inputValues, $validationRules, [], $fieldsCaptions);

			if ($validator->fails()){

				return $validator->errors();
			}
		}

		return false;
	}

	function checkAccess($crudCode, $checkAction){

		return crud_roles()->first(function ($role, $key) use ($crudCode, $checkAction){

		    if ($role->users->first(function ($user, $key) {
					return $user->id == Auth::user()->id;
				})) {

			 	return $role->forms->first(function ($form, $key) use ($crudCode, $checkAction){

				    return ($form->code == $crudCode) && $form->pivot->{$checkAction};
			    });
			 }

			 return false;
		});
	}

	function saveCrudItem($crud, $inputValues){

		if (is_string($crud))
			$crud = $this->getCrudByCode($crud);

		if (isset($inputValues[$crud['id']])) {

			$id = $inputValues[$crud['id']];

			$item = $this->getModelByCrud($crud, $id, true);
		} else {

			$item = new $crud['model'];
		}

		$itemSaved = false;

		// sort fields - relations field to end

		$crud['fields'] = array_sort($crud['fields'], function($val, $key){
			return $val['type'] == 'relation' ? 1 : 0;
		});

		foreach ($crud['fields'] as $field){

			if ($field['type'] == 'dynamic'){

				if ($field['additional']['type'] == 'field'){

					$field['type'] = $inputValues[$field['additional']['from']];
				}

				if ($field['additional']['type'] == 'related'){

					[$dynamicRelation, $dynamicField] = explode('.', $field['additional']['from']);

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

			if ( ($field['type'] == 'relation') && $field['json'] ) {

				$item[$field['name']] = $inputValues[$field['name']];

			}

			if ( ($field['type'] == 'relation') && !$field['json'] ){

				$crudField = null;

				if (isset($inputValues[snake_case($field['name'])])) {
					$crudField = $inputValues[snake_case($field['name'])];
				} else {
					if (isset($crudField)) unset($crudField);
				}

				$relationCrud = $this->getCrudByCode($field['relation']['crud']['code']);

				/************************************ belongsTo ************************************/

				if ( $field['relation']['type'] == 'belongsTo'){

					if (!isset($crudField) || empty($crudField)){
						$item->{$field['name']}()->dissociate();

						continue;
					}

					$relatedItem = $this->getModelByCrud($relationCrud, $crudField[$relationCrud['id']], false);

					$item->{$field['name']}()->associate($relatedItem);

					continue;
				}

				/************************************ hasMany ************************************/

				if ($field['relation']['type'] == 'hasMany'){

					if (!$itemSaved) {
						$item->save();
					}

					$targetRelationIds = isset($crudField) && count($crudField) ? array_column($crudField, $relationCrud['id']) : [];

					$relationShips = $this->getModelRelations($item);
					$relationForeignKey = $relationShips[$field['name']]['foreign_key'];

					// disassociate
					if (isset($id)){

						$item->{$field['name']}()->whereNotIn($relationCrud['id'], $targetRelationIds)->update([
							$relationForeignKey => null
						]);
					}

					// save missing items
					if (isset($crudField) && !empty($crudField)){

						$relationCrud['model']::whereIn($relationCrud['id'], $targetRelationIds)->update([
							$relationForeignKey => $item->{$crud['id']}
						]);
					}

					continue;
				}

				/************************************ belongsToMany ************************************/

				if ($field['relation']['type'] == 'belongsToMany'){

					if (!$itemSaved) {
						$item->save();
					}

					$relationIds = [];

					if (isset($crudField) && !empty($crudField)) {


						// extract json root fields names
						$jsonPivotRootFields = [];

						if (isset($field['relation']['pivot']) && count($field['relation']['pivot'])){

							array_walk($field['relation']['pivot'], function($val, $key)use(&$jsonPivotRootFields){
								if (isset($val['json']) && $val['json']) {

									$jsonPivotRootFields[ explode('->', $val['name'])[0] ] = '[]';
								}
							});
						}

						forEach ( $crudField as $crudRelatedItem ) {

							if ( isset( $field['relation']['pivot'] ) && count($field['relation']['pivot']) ) {

                                $pivotValues = [];

                                forEach (array_sort($field['relation']['pivot'], 'json') as $pivotField) {

                                    if (isset($crudRelatedItem['pivot'][$pivotField['name']])) {


                                        if (!$pivotField['json']) {
                                            $pivotValues[$pivotField['name']] = $crudRelatedItem['pivot'][$pivotField['name']];
                                        } else {

                                            $jsonFieldPath = explode('->', $pivotField['name']);

                                            $jsonFieldRoot = $jsonFieldPath[0];

                                            unset($jsonFieldPath[0]);

                                            $jsonFieldPath = implode('.', $jsonFieldPath);

                                            array_set($pivotValues[$jsonFieldRoot], $jsonFieldPath, $crudRelatedItem['pivot'][$pivotField['name']]);
                                        }
                                    }
                                }

                                if (count($jsonPivotRootFields)) {

                                    foreach ($jsonPivotRootFields as $jsonPivotRootField => $jsonPivotRootFieldValue) {
                                        $pivotValues[$jsonPivotRootField] = (isset($pivotValues[$jsonPivotRootField]) ? json_encode($pivotValues[$jsonPivotRootField]) : $jsonPivotRootFieldValue);
                                    }
                                }

                                $relationIds[$crudRelatedItem[$relationCrud['id']]] = $pivotValues;
                            } else {
								$relationIds[] = $crudRelatedItem[ $relationCrud['id'] ];
							}
						}
					}


					$item->{$field['name']}()->sync($relationIds);

				}

				/************************************ hasOne ************************************/

				if ($field['relation']['type'] == 'hasOne'){

					if (!$itemSaved) {
						$item->save();
					}

					// TODO: check for empty $crudField and disassociate

					$relatedItem = $this->getModelByCrud($relationCrud, $crudField[$relationCrud['id']], false);

					$item->{$field['name']}()->save($relatedItem);
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

//				if(!isset($id) || ($item->parent->id <> $inputValues[ $crud['tree']['parent']])){
				if(!isset($id) || ($item->parent->id <> $inputValues[ 'parent_id' ])){
					//$item->order = call_user_func($crud['model']."::where", $crud['tree']['parent'], '=', $item->parent->id)->
					$item->order = call_user_func($crud['model']."::where", 'parent_id', '=', $item->parent->id)->
						where($crud['id'],'<>', $item->id)->max('order') + 1;
				} else {
					$item->order = $inputValues['order'];
				}

			} else {
				$item->level =  1;

				//$item->order = call_user_func($crud['model']."::whereNull", $crud['tree']['parent'])->

				$item->order = call_user_func($crud['model']."::whereNull", 'parent_id')->
					where($crud['id'],'<>', $item->id)->max('order') + 1;

				$item->path = str_pad($item->id, 4, '0', STR_PAD_LEFT) . ';';;
			}

			$item->save();

		}

		return $item;

	}

}