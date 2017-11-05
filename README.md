# Laravel crud admin

Builded with <a href="https://vuejs.org" target="_blank">Vue.js</a>, <a href="https://bulma.io" target="_blank">bulma.io</a>


Manage your data 
<p align="center"><img src="http://i58.photobucket.com/albums/g266/vshapovalov/crud-table_zpsdfpfsrq0.png"></p>
<p align="center"><img src="http://i58.photobucket.com/albums/g266/vshapovalov/edit-panel_zpseslpfk7v.png"></p>

Store files in media library
<p align="center"><img src="http://i58.photobucket.com/albums/g266/vshapovalov/crud-table_zpsdfpfsrq0.png"></p>

## Crud admin has 

Collection of controls:

  textbox 
    mode - password, masked, slugify
  colorbox
  checkbox
  dropdown
    mode - single, multi
  datepicker
    mode - date, datetime
  richedit
    by tinymce
  image
    mode - single, multi
  dynamic
  relation
    hasOne, hasMany, belongsTo, belongsToMany(also pivot)
    
Fields validation
  using laravel validator

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

### 1. Cruds declaration 

Just declare crud for your eloquent model in config/cruds.php and refresh crud admin page

There is still a lot of work ahead - roles, localization, widgets, etc. ;]
