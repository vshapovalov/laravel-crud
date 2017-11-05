<?php


namespace Vshapovalov\Crud;


trait TreeableTrait {

	public function parent(){
		return $this->belongsTo(self::class, 'parent_id', 'id');
	}

	public function children(){
		return $this->hasMany(self::class, 'parent_id', 'id');
	}

	public function paddedKey(){
		return str_pad($this->{$this->getKeyName()}, 4,'0', STR_PAD_LEFT) .';';
	}

}