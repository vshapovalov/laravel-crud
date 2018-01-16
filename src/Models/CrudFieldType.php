<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;

class CrudFieldType extends Model
{

	public $timestamps = false;

	protected $table = 'crud_field_types';
	protected $primaryKey ='id';


}
