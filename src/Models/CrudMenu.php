<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Vshapovalov\Crud\RelationshipsTrait;

class CrudMenu extends Model
{

	function items(){
		return $this->hasMany(CrudMenu::class, 'parent_id', 'id');
    }

	function parent(){
		return $this->belongsTo(CrudMenu::class, 'parent_id', 'id');
	}

	function roles(){
		return $this->belongsToMany(Role::class, 'role_menu', 'menu_id', 'role_id');
	}
}
