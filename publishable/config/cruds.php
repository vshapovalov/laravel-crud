<?php
/*
	Crud options:
				'name'    => 'Пользователи', // name of crud, displays in admin navbar
				'code'    => 'users', // crud code and url for api requests
				'model'   => 'App\User', // crud using model
				'id'      => 'id', // model's primarykey
				'display' => 'name', // crud field for crud table and controls
				'visible' => true, // is crud visible in admin menu
				'type'    => 'list', // crud type ['list', 'tree'], if you want to use tree type, then use Treeable trait for model
				'scopes'  => [  // model scopes for getting crud rows
							'byUser'    => [
								'name'   => 'orderByName', // model scope function name, example, orderByName for scopeOrderByName
								'params' => [ // params for crud model scopes when crud in pick mode on edit panel, resolved from root item, injected in scope
									'id'
								]
							],
							'onlyWithImages' => [ // example without params
								'name' => 'onlyWithImages'
							]
						],

	Crud field options:

				'name'       => 'password' // model's field or relation name
				'caption'    => 'Пароль' // field caption for crud table and control
				'type'       => 'textbox' // crud field type, control type
				'visibility' => [ 'browse', 'edit', 'add' ] // visibility and state of control in crud table and edit panel
				'tab'        => 'Основные параметры' // tab name for control in edit panel
				'validation' => 'required|string:255' // laravel validation rule, except relation field type
				'readonly'   => true // is field readonly on editpanel in [edit, add] states
				'additional' => [] // additional control options
				'relation' => [] // options for fields of relation type
				'dynamic'    => [] // options for fields of dynamic type

	Crud field types:

			textbox - simple textbox
				has additional options: additional: ['slugify' => 'depending field name']
				has additional options: additional: ['mode' => 'password']
				has additional options: additional: ['mode' => 'masked', 'mask' => '+7(777)000-00-00']

			colorbox - color picker based on html5 input[type="color"]

			checkbox - checkbox based on input[type="checkbox"]

			textarea - simple textarea

			datepicker - date picker, has additional options
				additional: ['mode' => ['date', 'datetime']]

			richedit - richeditor by tinymce

			image - image picker, based on crud media library, has additional options
				additional: ['mode' => ['single', 'multi']] // 'multi' is default

			dynamic - field type depends on other crud model field, field has options

				'dynamic'    => [
					'type' => 'relation', // relation type
					'from' => 'crudFieldType.code' // {relation field name}.{relation field attribute}
								or
					'type' => 'field', // field type
					'from' => 'fieldtype' // field name
				]

			relation - relation field type, field has options

				'relation'   => [
						'name'  => 'colors', // relation name
						'crud'  => 'colors', // crud used for field control, must be declared in crud config
						'type'  => 'belongsToMany', // relation type, one of ['belongsTo', 'belongsToMany', 'hasOne', 'hasMany']
						'pivot' => [ // pivot fields options if using pivot table, relation should have method ->withPivot([...])
							'fields' => [
								'qty'         => [
									'name'       => 'qty',      // pivot field name
									'caption'    => 'Изображения', // pivot field caption for crud table
									'type'       => 'image',    // control type for pivot field,
																// can be one of types:
																//   textbox, checkbox, textarea, image, richedit ... not relations, may be later

									'additional' => [           // additional field options, example for image field type
										'mode' => 'multi'
									]
								],
								'publishDate' => [
									'name'       => 'publishDate',
									'caption'    => 'Дата',
									'type'       => 'datepicker',
									'additional' => [
										'mode' => 'date'
									]
								],
							]
						]
					]

	 */




return
	[
		/* route prefixes */
		'crud_prefix' => 'cruds',
		'media_prefix' => 'media',

		/* cruds declaration */
		'list' =>
		[
			/*********************************** USERS *************************************/

//			[
//				'name'    => 'Пользователи',
//				'code'    => 'users',
//				'model'   => 'App\User',
//				'id'      => 'id',
//				'display' => 'name',
//				'visible' => true,
//				'type'    => 'list',
//				'meta'    => [
//					'fields' => [
//						'name'      => [
//							'name'       => 'name',
//							'caption'    => 'Пользователь',
//							'type'       => 'textbox',
//							'visibility' => [ 'browse', 'edit', 'add' ],
//							'tab'        => 'Основные параметры',
//							'validation'   => 'required',
//						],
//						'email'      => [
//							'name'       => 'email',
//							'caption'    => 'E-mail',
//							'type'       => 'textbox',
//							'visibility' => [ 'browse', 'edit', 'add' ],
//							'tab'        => 'Основные параметры',
//							'validation'   => 'required',
//						],
//						'password'      => [
//							'name'       => 'password',
//							'caption'    => 'Пароль',
//							'type'       => 'textbox',
//							'visibility' => [ 'edit', 'add' ],
//							'tab'        => 'Основные параметры',
//							'additional' => [
//								'mode' => 'password'
//							],
//						]
//					]
//				]
//			],


			/********************************** MENU ITEMS *************************************/
			[
				'name'    => 'Меню',
				'code'    => 'menuitems',
				'model'   => 'Vshapovalov\Crud\Models\MenuItem',
				'id'      => 'id',
				'display' => 'title',
				'visible' => true,
				'type'    => 'tree',
				'tree'    => [
					'parent' => 'parent_id'
				],
				'meta'    => [
					'fields' => [
						'title'      => [
							'name'       => 'title',
							'caption'    => 'Заголовок',
							'type'       => 'textbox',
							'visibility' => [ 'browse', 'edit', 'add' ],
							'tab'        => 'Основные параметры',
							'validation'   => 'required|string|max:191',
							'additional' => [
								'slugify' => 'url'
							]
						],
						'parent' => [
							'name'       => 'parent',
							'caption'    => 'Главное меню',
							'type'       => 'relation',
							'visibility' => [ 'browse', 'edit', 'add' ],
							'tab'        => 'Основные параметры',
							'relation'   => [
								'name' => 'parent',
								'crud' => 'menuitems',
								'type' => 'belongsTo'
							],
						],
						'url'      => [
							'name'       => 'url',
							'caption'    => 'Ссылка',
							'type'       => 'textbox',
							'visibility' => [ 'browse', 'edit', 'add' ],
							'tab'        => 'Основные параметры',
						],
						'code'      => [
							'name'       => 'code',
							'caption'    => 'Код',
							'type'       => 'textbox',
							'visibility' => [ 'browse', 'edit', 'add' ],
							'tab'        => 'Основные параметры',
						],
					]
				]
			],

			/************************ CRUD FIELD TYPES ********************/
			[
				'name'    => 'CRUD типы полей',
				'code'    => 'crudfieldtypes',
				'model'   => 'Vshapovalov\Crud\Models\CrudFieldType',
				'id'      => 'id',
				'display' => 'name',
				'items'   => '',
				'visible' => true,
				'type'    => 'list',
				'meta'    => [
					'fields' => [
						'name' => [
							'name'       => 'name',
							'caption'    => 'Название',
							'type'       => 'textbox',
							'visibility' => [ 'browse', 'edit', 'add' ],
							'tab'        => 'Основные параметры',
							'validation'   => 'required|string|max:191',
						],
						'code' => [
							'name'       => 'code',
							'caption'    => 'Код',
							'type'       => 'textbox',
							'visibility' => [ 'browse', 'edit', 'add' ],
							'tab'        => 'Основные параметры',
							'validation'   => 'required|string|max:191',
						]
					]
				]
			],


			/************************ ADMIN SETTING GROUPS ********************/

			[
				'name'    => 'Группы настроек',
				'code'    => 'adminsettinggroups',
				'model'   => 'Vshapovalov\Crud\Models\AdminSettingGroup',
				'id'      => 'id',
				'display' => 'name',
				'items'   => '',
				'visible' => true,
				'type'    => 'list',
				'meta'    => [
					'fields' => [
						'name' => [
							'name'       => 'name',
							'caption'    => 'Название',
							'type'       => 'textbox',
							'visibility' => [ 'browse', 'edit', 'add' ],
							'tab'        => 'Основные параметры',
							'validation'   => 'required|string|max:191',
						],
						'code' => [
							'name'       => 'code',
							'caption'    => 'Код',
							'type'       => 'textbox',
							'visibility' => [ 'browse', 'edit', 'add' ],
							'tab'        => 'Основные параметры',
							'validation'   => 'required|string|max:191',
						]
					]
				]
			],

			/************************ ADMIN SETTINGS ********************/

			[
				'name'    => 'Настройки',
				'code'    => 'adminsettings',
				'model'   => 'Vshapovalov\Crud\Models\AdminSetting',
				'id'      => 'id',
				'display' => 'name',
				'visible' => true,
				'type'    => 'list',
				'meta'    => [
					'fields' => [
						'name'              => [
							'name'       => 'name',
							'caption'    => 'Название',
							'type'       => 'textbox',
							'visibility' => [ 'browse', 'edit', 'add' ],
							'tab'        => 'Основные параметры',
							'validation'   => 'required|string|max:191',
						],
						'key'               => [
							'name'       => 'key',
							'caption'    => 'Ключ',
							'type'       => 'textbox',
							'visibility' => [ 'browse', 'edit', 'add' ],
							'tab'        => 'Основные параметры',
							'validation'   => 'required|string|max:191',
						],
						'adminSettingGroup' => [
							'name'       => 'adminSettingGroup',
							'caption'    => 'Группа настроек',
							'type'       => 'relation',
							'relation'   => [
								'name' => 'adminSettingGroup',
								'crud' => 'adminsettinggroups',
								'type' => 'belongsTo'
							],
							'visibility' => [ 'edit', 'add' ],
							'tab'        => 'Настройки',
							'validation'   => 'required',
						],
						'crudFieldType'     => [
							'name'       => 'crudFieldType',
							'caption'    => 'Тип поля',
							'type'       => 'relation',
							'relation'   => [
								'name' => 'crudFieldType',
								'crud' => 'crudfieldtypes',
								'type' => 'belongsTo'
							],
							'visibility' => [ 'edit', 'add' ],
							'tab'        => 'Настройки',
							'validation'   => 'required',
						],
						'value'             => [
							'name'       => 'value',
							'caption'    => 'Значение',
							'type'       => 'dynamic',
							'dynamic'    => [
								'type' => 'relation',
								'from' => 'crudFieldType.code'
							],
							'visibility' => [ 'browse', 'edit' ],
							'tab'        => 'Основные параметры',
						]
					]
				]
			],
		]
	];