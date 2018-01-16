<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;


class CrudField extends Model
{
    function relation(){
    	return $this->belongsTo(CrudRelation::class, 'crud_relation_id')->with(['crud']);
    }
}
