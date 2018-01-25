<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;


class CrudField extends Model
{
    protected $fillable = [
	'crud_form_id', 'name', 'caption', 'type', 'visibility', 'by_default',
	'json', 'readonly', 'description', 'tab', 'validation','additional',
	'crud_relation_id', 'order' , 'columns'

    ];

    function relation(){
    	return $this->belongsTo(CrudRelation::class, 'crud_relation_id')->with(['crud']);
    }
}
