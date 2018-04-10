<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;

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


    	cache()->forget('crud.list');

		return parent::save( $options );
	}

    public function delete() {

        cache()->forget('crud.list');

        return parent::delete();
    }

	function scopes(){
    	return $this->hasMany(CrudScope::class, 'crud_form_id', 'sur_id')->with(['params']);
    }

}
