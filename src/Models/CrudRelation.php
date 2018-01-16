<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;
use Vshapovalov\Crud\RelationshipsTrait;

class CrudRelation extends Model
{

	function crud(){
		return $this->belongsTo(CrudForm::class, 'crud_form_id');
	}

	function pivot(){
		return $this->hasMany(CrudField::class, 'crud_relation_id')->whereNull('crud_form_id');
	}

}
