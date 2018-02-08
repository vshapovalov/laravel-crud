<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Vshapovalov\Crud\DataableTrait;

class Role extends Model
{
	protected static $dataKeyField = 'code';
	protected static $dataValueField = 'name';

	use DataableTrait;

    function menus(){
    	return $this->belongsToMany(CrudMenu::class,
		    'role_menu',
		    'role_id',
		    'menu_id');
    }

	function forms(){
		return $this->belongsToMany(CrudForm::class,
			'role_crud_form',
			'role_id',
			'crud_form_id')->withPivot(['add', 'edit', 'delete']);
	}

	function users(){
		return $this->belongsToMany(User::class, 'user_role');
	}

}
