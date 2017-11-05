<?php
/**
 * Created by PhpStorm.
 * User: dr_sharp
 * Date: 10.10.2017
 * Time: 13:18
 */

namespace Vshapovalov\Crud\Facades;

use Illuminate\Support\Facades\Facade;

class Crud extends Facade {

	protected static function getFacadeAccessor() {
		return 'crud';
	}

}