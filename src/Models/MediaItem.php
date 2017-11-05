<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;

class MediaItem extends Model
{
    protected $table = 'media_items';

    protected $fillable = ['title', 'path', 'parent_id', 'type'];

    function parent(){
    	return $this->belongsTo(MediaItem::class, 'id', 'parent_id');
    }

	function children(){
		return $this->hasMany(MediaItem::class, 'parent_id', 'id');
	}

}
