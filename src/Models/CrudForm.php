<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;


class CrudForm extends Model
{

    protected $primaryKey = 'sur_id';

    function fields(){
    	return $this->hasMany(
    		CrudField::class,
		    'crud_form_id',
		    'sur_id')
	                ->orderBy('order')
	                ->orderBy('name')
	                ->with(['relation.crud', 'relation.pivot']);
    }

	public function save( array $options = [] ) {

    	Cache::forget('crud.list');

		return parent::save( $options );
	}

	function scopes(){
    	return $this->hasMany(CrudScope::class, 'crud_form_id', 'sur_id')->with(['params']);
    }

}
