<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Support\Facades\Cache;
use Vshapovalov\Crud\TreeableTrait;

class MenuItem extends CrudModel
{
	use TreeableTrait;

	protected $table ='menu_items';
	protected $primaryKey ='id';

	public function save( array $options = [] ) {

		if ($this->id) {
			// reset rendered menu cache

			if (count($itemsWithCode = MenuItem::whereNotNull('code')->get())){

				foreach ( $itemsWithCode as $item ) {
					if ($item->code) Cache::forget('menu.'.$item->code);
				}
			}
		}

		return parent::save( $options );
	}


}
