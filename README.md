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

### 4. import database/scripts/crud_forms.sql into your db

Check http://app_url/cruds

## How it works

- declare crud form for your model in admin panel and refresh crud admin page

There is still a lot of work ahead - localization, etc. ;]

### Crud options

All crud forms can be edited from admin panel

Systemp options->Crud forms

### Crud field addtional options(json type)

**textbox** - simple textbox
- has additional options: additional: {"slugify":"depending_field_name"}
- has additional options: additional: {"mode":"password"}
- has additional options: additional: {"mode":"masked", "mask":"+7(777)000-00-00"}

**colorbox** - color picker based on html5 input[type="color"]

**checkbox** - checkbox based on input[type="checkbox"]

**textarea** - simple textarea

**datepicker** - date picker, has additional options {"mode":"date|datetime"}

**dropdown** - simple dropdown, has additional options

additional:
{
    "mode:":"single|multi", 
    "values": [ 
        {"key":0, "value":"DRAFT"}, 
        {"key":1, "value":"PUBLISHED"} 
    ] 
}

**richedit** - richeditor by tinymce

**image** - image picker, based on crud media library, has additional options 

{
    "mode":"multi|single",
    "resize": { "width":1000, "height": null, "quality": 90},
    "thumbnails": [
        { "name":"medium", "scale":50},
        { "name":"small", "scale":25},
        { "name":"cropped", "crop": {"width": 250, "height": 250 } }
    ] 
}


**dynamic** - field type depends on other crud model field, field has options

{ "type":"related", "from": "crudFieldType.code"} or { "type":"field", "from": "fieldtype"} 

**relation** - relation field type, field has options

addional for pivot fields:

{ "buttons": [ "add", "edit", "pick", "delete_all" ]}


### Media library


Media library can resize, create thumbnails for uploaded images by default settings, also crud image field have additional options for resize image

config/cruds.php
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

Admin menu list can be edited from admin panel

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


Admin menu crud form 
    name - user_component
    caption - User component test

    // user component must be registered by action, which specified in user component script
    // example of user component can be found in %package%/resource/assets/js/example-user-component/
    
    action - user:testcomponent:mount
    