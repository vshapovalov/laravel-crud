<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{

	protected $table = 'admin_settings';
	protected $primaryKey = 'id';

	function adminSettingGroup(){
		return $this->belongsTo(AdminSettingGroup::class,'admin_setting_group_id', 'id');
	}

	function crudFieldType(){
		return $this->belongsTo(CrudFieldType::class,'crud_field_type_id', 'id');
	}
}
