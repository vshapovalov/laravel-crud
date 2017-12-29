# Laravel crud admin

Builded with <a href="https://vuejs.org" target="_blank">Vue.js</a>, <a href="https://bulma.io" target="_blank">bulma.io</a>

Manage your data

Store files in media library

![crud table](http://teacup.kz/laravel-crud/crud-table.png)
![edit_panel](http://teacup.kz/laravel-crud/edit-panel.png)
![media_library](http://teacup.kz/laravel-crud/media-library.png)

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

There is still a lot of work ahead - roles, localization, etc. ;]

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
  'json'       => true, // indicates that field uses value of json field, in this case name must be like 'meta->bio->gender' 
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

also, if you using json field type, then cast model json prop to array

```php

class User extends Authenticatable
{
    use RelationshipsTrait;
    
    protected $casts = [
        // name of json db field
    	'meta' => 'array'
    ];
    
    // use mutators for for pretty field access 
    function getSexAttribute(){
        return $this['meta->bio->gender'];
    }
    
    function setSexAttribute($val){
        $this['meta->bio->gender'] = $val;
    }
}
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

**dropdown** - simple dropdown, has additional options

```php
additional => [
    'mode' => 'single', // single, multi
    'values' => [
        [ 'key' => 0, 'value' => 'DRAFT'],
        [ 'key' => 1, 'value' => 'PUBLISHED']
    ]
]
```

**richedit** - richeditor by tinymce

**image** - image picker, based on crud media library, has additional options 
```php
[
    'mode' => 'multi', // multi or single, 'multi' is default
    'resize' => [
            'width' => 1000,
            'height' => null,
            'quality' => 90
        ],
    'thumbnails' => [
        [
            'name' => 'medium',
            'scale' => 50
        ],
        [
            'name' => 'small',
            'scale' => 25
        ],
        [
            'name' => 'cropped',
            'crop' => [
                'width' => 250,
                'height' => 250,
            ]
        ]
    ] 
]
```

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

### Media library


Media library can resize, create thumbnails for uploaded images by default settings, also crud image field have additional options for resize image
```php
'media_default_settings' => [
    /* for example
        'resize' => [
            'width' => 1000,
            'height' => null,
            'quality' => 90
        ],
        'thumbnails' => [
            [
                'name' => 'medium',
                'scale' => 50
            ],
            [
                'name' => 'small',
                'scale' => 25
            ],
            [
                'name' => 'cropped',
                'crop' => [
                    'width' => 250,
                    'height' => 250,
                ]
            ]
        ]
    */
],
```

### Menu options

Menu section contains menu items:

```php
[
    // unique name of menu item
    'name' => 'media-library',

    // caption of menu item
    'caption' => 'Медиа библиотека',

    // bus event name, 'medialibrary:mount' is default for media library,
    // 'crud:%crudcode%:mount' is default for cruds
    'action' => 'medialibrary:mount'

    // this component will be mount after admin panel loaded
    'default' => true,

    // items can be grouped
    // array of nested items, contains same item
    // nested items can contains items too
    'items' => [

    ]
],
```

Also you can use own Vue components(some widgets), just add them in components and menu sections.
User component can use vue, lodash, axios, jquery, because they are bundled in admin.js and declared as window obj props

```php
'components' => [
    [
        'name' => 'test-component', // just simple name
        'path' => '/js/test-components.js' // path to component, must be absolute
    ]
],
```

```php
[
    'name' => 'user_component',
    'caption' => 'User component test',

    // user component must be registered by action, which specified in user component script
    // example of user component can be found in %package%/resource/assets/js/example-user-component/
    'action' => 'user:testcomponent:mount'
] 
```