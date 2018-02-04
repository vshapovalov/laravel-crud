<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrudFormsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    /** relations */

	    $role = new \Vshapovalov\Crud\Models\Role();

	    $role->code = 'admin';
	    $role->name = 'Администратор';
	    $role->save();


	    $user = new \App\User();
	    $user->name = 'admin';
	    $user->email = 'admin@admin.com';
	    $user->password = '$2y$10$yXXgm2NZ5pEIFRl6U5C4sOiamvuMv6pC/5R/Cadr3Rr.UoYtxM.Q2';
	    $user->save();

	    /******************* crud forms ******************/

	    $formUser = new \Vshapovalov\Crud\Models\CrudForm();
	    $formUser->name = 'Пользователи';
	    $formUser->code = 'users';
	    $formUser->model = 'App\\User';
	    $formUser->id = 'id';
	    $formUser->display = 'name';
	    $formUser->type = 'list';
	    $formUser->save();

	    $formForm = new \Vshapovalov\Crud\Models\CrudForm();
	    $formForm->name = 'CRUD формы';
	    $formForm->code = 'crudforms';
	    $formForm->model = 'Vshapovalov\\Crud\\Models\\CrudForm';
	    $formForm->id = 'sur_id';
	    $formForm->display = 'name';
	    $formForm->type = 'list';
	    $formForm->save();

	    $formScopeParam = new \Vshapovalov\Crud\Models\CrudForm();
	    $formScopeParam->name = 'CRUD Scope params';
	    $formScopeParam->code = 'crudscopeparams';
	    $formScopeParam->model = 'Vshapovalov\\Crud\\Models\\CrudScopeParam';
	    $formScopeParam->id = 'id';
	    $formScopeParam->display = 'name';
	    $formScopeParam->type = 'list';
	    $formScopeParam->save();

	    $formScopes = new \Vshapovalov\Crud\Models\CrudForm();
	    $formScopes->name = 'CRUD Scopes';
	    $formScopes->code = 'crudscopes';
	    $formScopes->model = 'Vshapovalov\\Crud\\Models\\CrudScope';
	    $formScopes->id = 'id';
	    $formScopes->display = 'name';
	    $formScopes->type = 'list';
	    $formScopes->save();



	    $formFields = new \Vshapovalov\Crud\Models\CrudForm();
	    $formFields->name = 'CRUD поля';
	    $formFields->code = 'crudfields';
	    $formFields->model = 'Vshapovalov\\Crud\\Models\\CrudField';
	    $formFields->id = 'id';
	    $formFields->display = 'name';
	    $formFields->type = 'list';
	    $formFields->save();

	    $formRelation = new \Vshapovalov\Crud\Models\CrudForm();
	    $formRelation->name = 'CRUD связи';
	    $formRelation->code = 'crudrelations';
	    $formRelation->model = 'Vshapovalov\\Crud\\Models\\CrudRelation';
	    $formRelation->id = 'id';
	    $formRelation->display = 'crud.name';
	    $formRelation->type = 'list';
	    $formRelation->save();

	    $formCrudMenu = new \Vshapovalov\Crud\Models\CrudForm();
	    $formCrudMenu->name = 'Меню админки';
	    $formCrudMenu->code = 'crudmenu';
	    $formCrudMenu->model = 'Vshapovalov\\Crud\\Models\\CrudMenu';
	    $formCrudMenu->id = 'id';
	    $formCrudMenu->display = 'name';
	    $formCrudMenu->type = 'list';
	    $formCrudMenu->save();

	    $formRole = new \Vshapovalov\Crud\Models\CrudForm();
	    $formRole->name = 'Роли';
	    $formRole->code = 'roles';
	    $formRole->model = 'Vshapovalov\\Crud\\Models\\Role';
	    $formRole->id = 'id';
	    $formRole->display = 'name';
	    $formRole->type = 'list';
	    $formRole->save();

	    $formMenuItem = new \Vshapovalov\Crud\Models\CrudForm();
	    $formMenuItem->name = 'Меню сайта';
	    $formMenuItem->code = 'menuitems';
	    $formMenuItem->model = 'Vshapovalov\\Crud\\Models\\MenuItem';
	    $formMenuItem->id = 'id';
	    $formMenuItem->display = 'title';
	    $formMenuItem->type = 'tree';
	    $formMenuItem->save();

	    $formFieldType = new \Vshapovalov\Crud\Models\CrudForm();
	    $formFieldType->name = 'CRUD типы полей';
	    $formFieldType->code = 'crudfieldtypes';
	    $formFieldType->model = 'Vshapovalov\\Crud\\Models\\CrudFieldType';
	    $formFieldType->id = 'id';
	    $formFieldType->display = 'name';
	    $formFieldType->type = 'list';
	    $formFieldType->save();

	    $formSettingGroup = new \Vshapovalov\Crud\Models\CrudForm();
	    $formSettingGroup->name = 'Группы настроек';
	    $formSettingGroup->code = 'adminsettinggroups';
	    $formSettingGroup->model = 'Vshapovalov\\Crud\\Models\\AdminSettingGroup';
	    $formSettingGroup->id = 'id';
	    $formSettingGroup->display = 'name';
	    $formSettingGroup->type = 'list';
	    $formSettingGroup->save();

	    $formSetting = new \Vshapovalov\Crud\Models\CrudForm();
	    $formSetting->name = 'Настройки';
	    $formSetting->code = 'adminsettings';
	    $formSetting->model = 'Vshapovalov\\Crud\\Models\\AdminSetting';
	    $formSetting->id = 'id';
	    $formSetting->display = 'name';
	    $formSetting->type = 'list';
	    $formSetting->save();

	    /********* relations **********/

	    $relationScopeParam = new \Vshapovalov\Crud\Models\CrudRelation();
	    $relationScopeParam->type = 'hasMany';
	    $relationScopeParam->crud_form_id = $formScopeParam->sur_id;
	    $relationScopeParam->save();

	    $relationScopes = new \Vshapovalov\Crud\Models\CrudRelation();
	    $relationScopes->type = 'hasMany';
	    $relationScopes->crud_form_id = $formScopes->sur_id;
	    $relationScopes->save();

	    $relationFields = new \Vshapovalov\Crud\Models\CrudRelation();
	    $relationFields->type = 'hasMany';
	    $relationFields->crud_form_id = $formFields->sur_id;
	    $relationFields->save();

	    $relationForm = new \Vshapovalov\Crud\Models\CrudRelation();
	    $relationForm->type = 'belongsTo';
	    $relationForm->crud_form_id = $formForm->sur_id;
	    $relationForm->save();

	    $relationRelation = new \Vshapovalov\Crud\Models\CrudRelation();
	    $relationRelation->type = 'belongsTo';
	    $relationRelation->crud_form_id = $formRelation->sur_id;
	    $relationRelation->save();

	    $relationCrudMenu = new \Vshapovalov\Crud\Models\CrudRelation();
	    $relationCrudMenu->type = 'belongsTo';
	    $relationCrudMenu->crud_form_id = $formCrudMenu->sur_id;
	    $relationCrudMenu->save();

	    $relationRole = new \Vshapovalov\Crud\Models\CrudRelation();
	    $relationRole->type = 'belongsToMany';
	    $relationRole->crud_form_id = $formRole->sur_id;
	    $relationRole->save();

	    $relationUser = new \Vshapovalov\Crud\Models\CrudRelation();
	    $relationUser->type = 'belongsToMany';
	    $relationUser->crud_form_id = $formUser->sur_id;
	    $relationUser->save();

	    $relationCrudMenuBTM = new \Vshapovalov\Crud\Models\CrudRelation();
	    $relationCrudMenuBTM->type = 'belongsToMany';
	    $relationCrudMenuBTM->crud_form_id = $formCrudMenu->sur_id;
	    $relationCrudMenuBTM->save();

	    $relationMenuItem = new \Vshapovalov\Crud\Models\CrudRelation();
	    $relationMenuItem->type = 'belongsTo';
	    $relationMenuItem->crud_form_id = $formMenuItem->sur_id;
	    $relationMenuItem->save();

	    $relationSettingGroup = new \Vshapovalov\Crud\Models\CrudRelation();
	    $relationSettingGroup->type = 'belongsTo';
	    $relationSettingGroup->crud_form_id = $formSettingGroup->sur_id;
	    $relationSettingGroup->save();

	    $relationFieldType = new \Vshapovalov\Crud\Models\CrudRelation();
	    $relationFieldType->type = 'belongsTo';
	    $relationFieldType->crud_form_id = $formFieldType->sur_id;
	    $relationFieldType->save();


	    /********** fields **********/

	    $formUser->fields()->createMany(
	    	[

		        [
				    'name' => 'roles',
				    'caption' => 'Роли',
				    'type' => 'relation',
				    'visibility' => '["add","edit"]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Роли',
				    'validation' => null,
				    'additional' => null,
		            'crud_relation_id' => $relationRole->id,
				    'order' => 4,
				    'columns' => 12
			    ],
			    [
				    'name' => 'name',
				    'caption' => 'ФИО',
				    'type' => 'textbox',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 1,
				    'columns' => 6
			    ],
			    [
				    'name' => 'password',
				    'caption' => 'Пароль',
				    'type' => 'textbox',
				    'visibility' => '["add","edit"]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => null,
				    'additional' => '{ "mode":"password"}',
				    'crud_relation_id' => null,
				    'order' => 3,
				    'columns' => 6
			    ],
			    [
				    'name' => 'email',
				    'caption' => 'E-mail',
				    'type' => 'textbox',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 2,
				    'columns' => 6
			    ]

		    ]

	    );

	    $formForm->fields()->createMany(
	    	[

			    [
				    'name' => 'code',
				    'caption' => 'code',
				    'type' => 'textbox',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 2,
				    'columns' => 6
			    ],
			    [
				    'name' => 'name',
				    'caption' => 'name',
				    'type' => 'textbox',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 1,
				    'columns' => 6
			    ],
			    [
				    'name' => 'model',
				    'caption' => 'model',
				    'type' => 'textbox',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 3,
				    'columns' => 6
			    ],
			    [
				    'name' => 'id',
				    'caption' => 'id field',
				    'type' => 'textbox',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 4,
				    'columns' => 6
			    ],
			    [
				    'name' => 'display',
				    'caption' => 'display field',
				    'type' => 'textbox',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 5,
				    'columns' => 6
			    ],
			    [
				    'name' => 'type',
				    'caption' => 'type',
				    'type' => 'dropdown',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => 'list',
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => '{"mode":"single", "values": [ {"key":"list", "value":"list"}, {"key":"tree", "value":"tree"} ]}',
				    'crud_relation_id' => null,
				    'order' => 6,
				    'columns' => 6
			    ],
			    [
				    'name' => 'fields',
				    'caption' => 'fields',
				    'type' => 'relation',
				    'visibility' => '["add","edit"]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Поля',
				    'validation' => null,
				    'additional' => '{ "buttons": [ "add", "delete_all"]}',
				    'crud_relation_id' => $relationFields->id,
				    'order' => 7,
				    'columns' => 12
			    ],
			    [
				    'name' => 'scopes',
				    'caption' => 'Scopes',
				    'type' => 'relation',
				    'visibility' => '["add","edit"]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Scopes',
				    'validation' => null,
				    'additional' => '{ "buttons": [ "add", "delete_all"]}',
				    'crud_relation_id' => $relationScopes->id,
				    'order' => 8,
				    'columns' => 12
			    ]
		    ]
	    );

	    $formScopeParam->fields()->createMany(
			[
			    [
				    'name' => 'name',
				    'caption' => 'Param name',
				    'type' => 'textbox',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 0,
				    'columns' => 12
			    ]
			]
	    );

	    $formScopes->fields()->createMany([
			    [
				    'name' => 'name',
				    'caption' => 'name',
				    'type' => 'textbox',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 0,
				    'columns' => 12
			    ],
			    [
				    'name' => 'params',
				    'caption' => 'Scope params',
				    'type' => 'relation',
				    'visibility' => '["add","edit"]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => null,
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 1,
				    'columns' => 12
			    ]
	        ]
	    );

	    $formFields->fields()->createMany(
			[
			    [
				    'name' => 'name',
				    'caption' => 'name',
				    'type' => 'textbox',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 1,
				    'columns' => 6
			    ],
			    [
				    'name' => 'caption',
				    'caption' => 'caption',
				    'type' => 'textbox',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 2,
				    'columns' => 6
			    ],
			    [
				    'name' => 'type',
				    'caption' => 'type',
				    'type' => 'dropdown',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => '{ "mode": "single", "values": [ {"key":"textbox", "value":"textbox"}, {"key":"colorbox", "value":"colorbox"}, {"key":"checkbox", "value":"checkbox"}, {"key":"textarea", "value":"textarea"}, {"key":"datepicker", "value":"datepicker"}, {"key":"richedit", "value":"richedit"}, {"key":"image", "value":"image"}, {"key":"dynamic", "value":"dynamic"}, {"key":"relation", "value":"relation"}, {"key":"dropdown", "value":"dropdown"} ]}',
				    'crud_relation_id' => null,
				    'order' => 3,
				    'columns' => 6
			    ],
			    [
				    'name' => 'relation',
				    'caption' => 'relation',
				    'type' => 'relation',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => null,
				    'additional' => null,
				    'crud_relation_id' => $relationRelation->id,
				    'order' => 4,
				    'columns' => 6
			    ],
			    [
				    'name' => 'visibility',
				    'caption' => 'visibility',
				    'type' => 'dropdown',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => '[ "browse", "edit", "add" ]',
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => '{ "mode":"multi","values": [ {"key":"browse", "value": "browse"}, {"key":"add", "value": "add"}, {"key":"edit", "value": "edit"} ]}',
				    'crud_relation_id' => null,
				    'order' => 5,
				    'columns' => 6
			    ],
			    [
				    'name' => 'by_default',
				    'caption' => 'by_default',
				    'type' => 'textbox',
				    'visibility' => '["add","edit"]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => null,
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 6,
				    'columns' => 6
			    ],
			    [
				    'name' => 'validation',
				    'caption' => 'validation',
				    'type' => 'textbox',
				    'visibility' => '["add","edit"]',
				    'by_default' => 'required',
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => null,
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 7,
				    'columns' => 6
			    ],
			    [
				    'name' => 'tab',
				    'caption' => 'tab',
				    'type' => 'textbox',
				    'visibility' => '["add","edit"]',
				    'by_default' => 'Основные параметры',
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 9,
				    'columns' => 6
			    ],
			    [
				    'name' => 'json',
				    'caption' => 'json',
				    'type' => 'checkbox',
				    'visibility' => '["add","edit"]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => null,
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 11,
				    'columns' => 6
			    ],
			    [
				    'name' => 'readonly',
				    'caption' => 'readonly',
				    'type' => 'checkbox',
				    'visibility' => '["add","edit"]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => null,
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 12,
				    'columns' => 6
			    ],
			    [
				    'name' => 'description',
				    'caption' => 'description',
				    'type' => 'textarea',
				    'visibility' => '["add","edit"]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => null,
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 14,
				    'columns' => 12
			    ],
			    [
				    'name' => 'additional',
				    'caption' => 'additional',
				    'type' => 'textarea',
				    'visibility' => '["add","edit"]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => null,
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 13,
				    'columns' => 12
			    ],
			    [
				    'name' => 'order',
				    'caption' => 'order',
				    'type' => 'textbox',
				    'visibility' => '["add","edit"]',
				    'by_default' => '0',
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => null,
				    'order' => 9,
				    'columns' => 6
			    ],
			    [
				    'name' => 'columns',
				    'caption' => 'columns',
				    'type' => 'dropdown',
				    'visibility' => '["add","edit"]',
				    'by_default' => '12',
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => '{"mode":"single", "values": [ {"key": 12, "value":"12"}, {"key": 6, "value":"6"}, {"key": 4, "value":"4"}, {"key": 3, "value":"3"}]}',
				    'crud_relation_id' => null,
				    'order' => 10,
				    'columns' => 6
			    ]
			]
	    );

	    $formRelation->fields()->createMany(
	    	[

			    [
				    'name' => 'type',
				    'caption' => 'Тип',
				    'type' => 'dropdown',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => 'belongsTo',
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => '{"mode":"single", "values": [ {"key":"belongsTo", "value":"belongsTo"}, {"key":"belongsToMany","value":"belongsToMany"}, {"key":"hasOne","value":"hasOne"}, {"key":"hasMany","value":"hasMany"} ]}',
				    'crud_relation_id' => null,
				    'order' => 0,
				    'columns' => 12
			    ],
			    [
				    'name' => 'crud',
				    'caption' => 'crud',
				    'type' => 'relation',
				    'visibility' => '[ "browse", "edit", "add" ]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Основные параметры',
				    'validation' => 'required',
				    'additional' => null,
				    'crud_relation_id' => $relationForm->id,
				    'order' => 1,
				    'columns' => 12
			    ],
			    [
				    'name' => 'pivot',
				    'caption' => 'pivot',
				    'type' => 'relation',
				    'visibility' => '["add","edit"]',
				    'by_default' => null,
				    'json' => 0,
				    'readonly' => 0,
				    'description' => null,
				    'tab' => 'Pivot',
				    'validation' => null,
				    'additional' => null,
				    'crud_relation_id' => $relationFields->id,
				    'order' => 2,
				    'columns' => 12
			    ]
		    ]
	    );

	    $formCrudMenu->fields()->createMany(
			[
				[
					'name' => 'name',
					'caption' => 'name',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required',
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 1,
					'columns' => 6
				],
				[
					'name' => 'caption',
					'caption' => 'caption',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required',
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 2,
					'columns' => 6
				],
				[
					'name' => 'action',
					'caption' => 'action',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => null,
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 3,
					'columns' => 6
				],
				[
					'name' => 'status',
					'caption' => 'status',
					'type' => 'dropdown',
					'visibility' => '["add","edit"]',
					'by_default' => 'enabled',
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required',
					'additional' => '{"mode":"single", "values": [ {"key":"disabled", "value":"disabled"},{"key":"enabled", "value":"enabled"} ]}',
					'crud_relation_id' => null,
					'order' => 4,
					'columns' => 6
				],
				[
					'name' => 'order',
					'caption' => 'order',
					'type' => 'textbox',
					'visibility' => '["add","edit"]',
					'by_default' => '0',
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required',
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 5,
					'columns' => 6
				],
				[
					'name' => 'default',
					'caption' => 'default',
					'type' => 'checkbox',
					'visibility' => '["add","edit"]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => null,
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 6,
					'columns' => 6
				],
				[
					'name' => 'parent',
					'caption' => 'parent',
					'type' => 'relation',
					'visibility' => '["add","edit"]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => null,
					'additional' => null,
					'crud_relation_id' => $relationCrudMenu->id,
					'order' => 7,
					'columns' => 6
				]
			]

	    );

	    $formRole->fields()->createMany(
			[
				[
					'name' => 'code',
					'caption' => 'Код',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required',
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 0,
					'columns' => 6
				],
				[
					'name' => 'name',
					'caption' => 'Название',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required',
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 1,
					'columns' => 6
				],
				[
					'name' => 'users',
					'caption' => 'Пользователи',
					'type' => 'relation',
					'visibility' => '["add","edit"]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Пользователи',
					'validation' => null,
					'additional' => null,
					'crud_relation_id' => $relationUser->id,
					'order' => 2,
					'columns' => 12
				],
				[
					'name' => 'menus',
					'caption' => 'Доступ к меню',
					'type' => 'relation',
					'visibility' => '["add","edit"]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Доступ к меню',
					'validation' => null,
					'additional' => null,
					'crud_relation_id' => $relationCrudMenuBTM->id,
					'order' => 3,
					'columns' => 12
				]
			]
	    );

	    $formMenuItem->fields()->createMany(
			[
				[
					'name' => 'title',
					'caption' => 'Заголовок',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required|string|max:191',
					'additional' => '{"slugify":"url"}',
					'crud_relation_id' => null,
					'order' => 1,
					'columns' => 6
				],
				[
					'name' => 'parent',
					'caption' => 'Главное меню',
					'type' => 'relation',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => null,
					'additional' => null,
					'crud_relation_id' => $relationMenuItem->id,
					'order' => 3,
					'columns' => 6
				],
				[
					'name' => 'url',
					'caption' => 'Ссылка',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => '/',
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => null,
					'additional' => null,
					'crud_relation_id' => $relationMenuItem->id,
					'order' => 2,
					'columns' => 6
				],
				[
					'name' => 'code',
					'caption' => 'Код',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => null,
					'additional' => null,
					'crud_relation_id' => $relationMenuItem->id,
					'order' => 4,
					'columns' => 6
				]
			]

	    );

	    $formFieldType->fields()->createMany(
			[
				[
					'name' => 'name',
					'caption' => 'Название',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required|string|max:191',
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 1,
					'columns' => 6
				],
				[
					'name' => 'code',
					'caption' => 'Код',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required|string|max:191',
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 0,
					'columns' => 6
				]
			]

		);

	    $formSettingGroup->fields()->createMany(
			[
				[
					'name' => 'code',
					'caption' => 'Код',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required|string|max:191',
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 0,
					'columns' => 6
				],
				[
					'name' => 'name',
					'caption' => 'Название',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required|string|max:191',
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 0,
					'columns' => 6
				]
			]

	    );

	    $formSetting->fields()->createMany(
			[
				[
					'name' => 'name',
					'caption' => 'Название',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required|string|max:191',
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 1,
					'columns' => 6
				],
				[
					'name' => 'key',
					'caption' => 'Код',
					'type' => 'textbox',
					'visibility' => '[ "browse", "edit", "add" ]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required|string|max:191',
					'additional' => null,
					'crud_relation_id' => null,
					'order' => 0,
					'columns' => 6
				],
				[
					'name' => 'adminSettingGroup',
					'caption' => 'Группа настроек',
					'type' => 'relation',
					'visibility' => '["add","edit"]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required',
					'additional' => null,
					'crud_relation_id' => $relationSettingGroup->id,
					'order' => 2,
					'columns' => 6
				],
				[
					'name' => 'crudFieldType',
					'caption' => 'Тип поля',
					'type' => 'relation',
					'visibility' => '["add","edit"]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => 'required',
					'additional' => null,
					'crud_relation_id' => $relationFieldType->id,
					'order' => 3,
					'columns' => 6
				],
				[
					'name' => 'value',
					'caption' => 'Значение',
					'type' => 'dynamic',
					'visibility' => '["browse","edit"]',
					'by_default' => null,
					'json' => 0,
					'readonly' => 0,
					'description' => null,
					'tab' => 'Основные параметры',
					'validation' => null,
					'additional' => '{"type":"related","from":"crudFieldType.code"}',
					'crud_relation_id' => null,
					'order' => 4,
					'columns' => 12
				]
			]
		);

	    /********* menus ***********/

		$menu = new \Vshapovalov\Crud\Models\CrudMenu();
	    $menu->name = 'media-library';
	    $menu->caption = 'Медиа библиотека';
	    $menu->action = 'medialibrary:mount';
	    $menu->default = 0;
	    $menu->parent_id = null;
	    $menu->order = 1;
	    $menu->status = 'enabled';
	    $menu->save();

	    $systemMenu = new \Vshapovalov\Crud\Models\CrudMenu();
	    $systemMenu->name = 'system-menu';
	    $systemMenu->caption = 'Системные настройки';
	    $systemMenu->action = null;
	    $systemMenu->default = 0;
	    $systemMenu->parent_id = null;
	    $systemMenu->order = 2;
	    $systemMenu->status = 'enabled';
	    $systemMenu->save();

	    $menu = new \Vshapovalov\Crud\Models\CrudMenu();
	    $menu->name = 'crudmenu';
	    $menu->caption = 'Меню админ.панели';
	    $menu->action = 'crud:crudmenu:mount';
	    $menu->default = 0;
	    $menu->parent_id = $systemMenu->id;
	    $menu->order = 2;
	    $menu->status = 'enabled';
	    $menu->save();

	    $menu = new \Vshapovalov\Crud\Models\CrudMenu();
	    $menu->name = 'menuitems';
	    $menu->caption = 'Меню сайта';
	    $menu->action = 'crud:menuitems:mount';
	    $menu->default = 0;
	    $menu->parent_id = $systemMenu->id;
	    $menu->order = 1;
	    $menu->status = 'enabled';
	    $menu->save();

	    $menu = new \Vshapovalov\Crud\Models\CrudMenu();
	    $menu->name = 'crudfieldtypes';
	    $menu->caption = 'Типы полей';
	    $menu->action = 'crud:crudfieldtypes:mount';
	    $menu->default = 0;
	    $menu->parent_id = $systemMenu->id;
	    $menu->order = 1;
	    $menu->status = 'enabled';
	    $menu->save();

	    $menu = new \Vshapovalov\Crud\Models\CrudMenu();
	    $menu->name = 'adminsettinggroups';
	    $menu->caption = 'Группы настроек';
	    $menu->action = 'crud:adminsettinggroups:mount';
	    $menu->default = 0;
	    $menu->parent_id = $systemMenu->id;
	    $menu->order = 1;
	    $menu->status = 'enabled';
	    $menu->save();

	    $menu = new \Vshapovalov\Crud\Models\CrudMenu();
	    $menu->name = 'crudforms';
	    $menu->caption = 'CRUD формы';
	    $menu->action = 'crud:crudforms:mount';
	    $menu->default = 0;
	    $menu->parent_id = $systemMenu->id;;
	    $menu->order = 1;
	    $menu->status = 'enabled';
	    $menu->save();

	    $menu = new \Vshapovalov\Crud\Models\CrudMenu();
	    $menu->name = 'adminsettings';
	    $menu->caption = 'Настройки';
	    $menu->action = 'crud:adminsettings:mount';
	    $menu->default = 0;
	    $menu->parent_id = null;
	    $menu->order = 1;
	    $menu->status = 'enabled';
	    $menu->save();

	    $menu = new \Vshapovalov\Crud\Models\CrudMenu();
	    $menu->name = 'users';
	    $menu->caption = 'Пользователи';
	    $menu->action = 'crud:users:mount';
	    $menu->default = 0;
	    $menu->parent_id = null;
	    $menu->order = 1;
	    $menu->status = 'enabled';
	    $menu->save();

	    $menu = new \Vshapovalov\Crud\Models\CrudMenu();
	    $menu->name = 'roles';
	    $menu->caption = 'Роли';
	    $menu->action = 'crud:roles:mount';
	    $menu->default = 0;
	    $menu->parent_id = null;
	    $menu->order = 1;
	    $menu->status = 'enabled';
	    $menu->save();




    }
}
