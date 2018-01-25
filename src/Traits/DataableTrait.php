<?php

namespace Vshapovalov\Crud;

trait DataableTrait{

	protected static $data;

	public function save( array $options = [] ) {

		cache()->forget(self::class);

		return parent::save( $options );
	}

	private static function getDataItems(){

		if (!empty(self::$data)){
			return self::$data;
		}

		return self::$data = cache()->remember(self::class, 1440, function(){

			return self::all()->mapWithKeys(function($item){
				return [ $item->{self::$dataKeyField} => $item ];
			});
		});
	}

	public static function getData($dataKey, $valueField){

		$items = self::getDataItems();

    	if ($dataKey == null){
		    return $items;
	    } else {

		    if ($item = $items->get($dataKey)) {

			    return $item->{ $valueField ? $valueField : self::$dataValueField };
		    }

		    return '';
	    }
	}
}