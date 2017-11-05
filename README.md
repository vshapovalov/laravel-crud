# Laravel crud admin

Builded with <a href="https://vuejs.org" target="_blank">Vue.js</a>, <a href="https://bulma.io" target="_blank">bulma.io</a>

Manage your data

![crud table](http://i58.photobucket.com/albums/g266/vshapovalov/crud-table_zpsdfpfsrq0.png)
![crud edit panel](http://i58.photobucket.com/albums/g266/vshapovalov/edit-panel_zpseslpfk7v.png)

Store files in media library

![crud nedia](http://i58.photobucket.com/albums/g266/vshapovalov/crud-table_zpsdfpfsrq0.png)

## Crud admin has
  
- textbox (password, masked, slugify)
- colorbox
- checkbox
- dropdown (single, multi select)
- datepicker (date, datetime mode)
- richedit by tinymce
- image (single, multi pick)
- dynamic
- relation (hasOne, hasMany, belongsTo, belongsToMany with pivot)

**Fields validation using laravel validator**


## How to install

### 1. Config your application

Check db settings and APP_URL in .ENV file of your application

Make laravel auth scaffolding
```bash
php artisan make:auth
```

### 2. Require package

```bash
composer require vshapovalov/crud
```

>if you are using Laravel 5.4, add to config/app.php provider section

```bash
Vshapovalov\Crud\CrudServiceProvider::class,
```

### 3. Install crud

```bash
php artisan crud:install
```

Check http://app_url/cruds

## How it works

- inherit your eloquent model from CrudModel or use RelashionshipTrait[, TreeableTrait]
- declare crud for your model in config/cruds.php and refresh crud admin page

There is still a lot of work ahead - roles, localization, widgets, etc. ;]

### Crud options

```php
[
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
]
```

### Crud field options

```php
[
  'name'       => 'password', // model's field or relation name
  'caption'    => 'Пароль', // field caption for crud table and control
  'type'       => 'textbox', // crud field type, control type
  'visibility' => [ 'browse', 'edit', 'add' ], // visibility and state of control in crud table and edit panel
  'tab'        => 'Основные параметры', // tab name for control in edit panel
  'validation' => 'required|string:255', // laravel validation rule, except relation field type
  'description'=> 'введите уникальный пароль', // field description for control label
  'readonly'   => true, // is field readonly on editpanel in [edit, add] states
  'additional' => [], // additional control options
  'relation' => [], // options for fields of relation type
  'dynamic'    => [], // options for fields of dynamic type
]
```

### Crud field types

**textbox** - simple textbox
- has additional options: additional: ['slugify' => 'depending field name']
- has additional options: additional: ['mode' => 'password']
- has additional options: additional: ['mode' => 'masked', 'mask' => '+7(777)000-00-00']

**colorbox** - color picker based on html5 input[type="color"]

**checkbox** - checkbox based on input[type="checkbox"]

**textarea** - simple textarea

**datepicker** - date picker, has additional options ['mode' => ['date', 'datetime']]

**richedit** - richeditor by tinymce

**image** - image picker, based on crud media library, has additional options ['mode' => ['single', 'multi']] // 'multi' is default

**dynamic** - field type depends on other crud model field, field has options

```php
'dynamic'    => [
  'type' => 'relation', // relation type
  'from' => 'crudFieldType.code' // {relation field name}.{relation field attribute}
//        or
  'type' => 'field', // field type
  'from' => 'fieldtype' // field name
]
```

**relation** - relation field type, field has options

```php
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
```
