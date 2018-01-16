<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Role extends Model
{
    function menus(){
    	return $this->belongsToMany(CrudMenu::class,
		    'role_menu',
		    'role_id',
		    'menu_id');
    }

	function users(){
		return $this->belongsToMany(User::class, 'user_role');
	}

}
