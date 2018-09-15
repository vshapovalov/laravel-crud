# Laravel crud dashboard - CrudIt!

Builded with <a href="https://vuejs.org" target="_blank">Vue.js</a>, <a href="https://vuetifyjs.com" target="_blank">vuetifyjs</a>

Manage your data, store files.

![crud table](http://teacup.kz/laravel-crud/Screenshot_3.png)
![crud media](http://teacup.kz/laravel-crud/Screenshot_1.png)

## Dashboard components 
  
- textbox (password, masked, slugify, prefix, suffix)
- checkbox
- dropdown (single, multi select)
- datetimepicker 
- richedit by tinymce
- image/files (single, multi pick from gallery)
- relation (hasOne, hasMany, belongsTo, belongsToMany with pivot)

**Fields validation using laravel validator**

**Crud forms has access check by roles(select, add, edit, delete)**

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

Check url http://app_url/cruds

## How to create crud form

- Create eloquent model 
- Create crud form in dashboard
- Create dashboard menu item for crud form 
- Grant access to role for created crud form

There is still a lot of work ahead - localization, etc. ;]
All crud forms can be edited from admin panel

System options->Crud forms

### Crud components addtional options(json)

**textbox** - simple textbox
```json
{ "slugify": "fieldname_to_store_slug" }
{ "mode": "password" }
{ "mode": "masked", "mask":"+7(777)###-##-##" }
```

**datepicker** - date picker
```json
{ "mode":"date or datetime" } 
```
**dropdown** - simple dropdown, additional field required

```json
{
    "mode:": "single or multi", 
    "values": [ 
        { "key": "DRAFT", "value": "Draft" }, 
        { "key": "PUBLISHED", "value": "Published" } 
    ] 
}
```

**richedit** - richeditor by tinymce
```json
{ "size": "small or medium or large"}
```

**image** - image picker, pick images/files from media library 

```json
{
    "mode": "multi or single",
    "type": "file or image",
    "resize": { "width":1000, "height": null, "quality": 90 },
    "thumbnails": [
        { "name":"medium", "scale":50 },
        { "name":"small", "scale":25 },
        { "name":"cropped", "crop": {"width": 250, "height": 250 } },
        { "name":"fitted", "fit": {"width": 250, "height": 250 } }
    ] 
}
```

**relation** - relation field type, field has options
```json
{ "buttons": [ "add", "edit", "pick", "delete_all" ] , "mode": "simple or normal" }
```


### Media library

Media library can resize image, create thumbnails for uploaded images by default settings, also crud image field have additional options for resize image

config/cruds.php
```php
'media_default_settings' => [
    'resize' => [
        'width' => 1440,
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
        ],
        [
            'name' => 'fit320-240',
            'fit' => [
                'width' => 320,
                'height' => 240,
                'position' => 'center'
            ]
        ]
    ]
],
```

### Menu options

Dashboard menu items can be edited from admin panel

### Custom Vue components in dashboard

Also you can use own Vue components, just add them in components section of config/cruds.php

```php
'components' => [
    [
        'name' => 'test-component', // just simple name
        'path' => '/js/test-components.js' // path to component, must be absolute
    ]
],
```
 
simple component file

```js
Vue.component('test-component', require('./test-component.vue') );

let userComponent = {
    id: 'user-component-1',
    name: 'test-component',
    options: {
        isModal: false,
        counterStartValue: 100
    }
};

Bus.$on( 'user:testcomponent:mount', ()=> AdminManager.mountComponent( userComponent, true ) );
```
then create dashboard menu item< and set action to 'user:testcomponent:mount'
 
 
### Middlewares and user components in crud form

link js file in components section of cruds.php, use registerMiddleware function to make some action on events emmited by crud form or edit panel or add custom component.
if you did not call next(), the action will be interruted in some events:
       
- crud:on:edit
- crud:on:add
- crud:on:delete
- editpanel:before:save
 
```js
AdminManager.registerMiddleware( ( event, options, next )=>{

    if (event == 'crud:on:mount' && options.crud.code === 'users') {

        options.addComponents(
            [
                {
                    id: 'test-component',
                    name: 'test-component',
                    options: {
                        message: 'i am custom component'
                    }
                }
            ]
        );
    }

    next();
});
```

### How to link libraries

```php
'components' => [
    [
        'name' => 'jquery-slim-cdn', 
        'path' => 'https://code.jquery.com/jquery-3.3.1.slim.js'
    ]
],
```
 
Custom components can use Lodash, Axios, they are declared as window obj props

### How to create crud form

watch this guide (coming soon)

### How to make custom component

watch this guide (coming soon)
