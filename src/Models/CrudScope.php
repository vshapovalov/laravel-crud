<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;

class CrudScope extends Model
{
	protected $fillable = ['name'];

    function params(){
    	return $this->hasMany(CrudScopeParam::class);
    }

}
