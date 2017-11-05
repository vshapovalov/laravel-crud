<?php

namespace Vshapovalov\Crud\Models;

use Vshapovalov\Crud\TreeableTrait;

class MenuItem extends CrudModel
{
	use TreeableTrait;

	protected $table ='menu_items';
	protected $primaryKey ='id';

}
