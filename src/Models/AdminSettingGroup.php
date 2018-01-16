<?php

namespace Vshapovalov\Crud\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSettingGroup extends Model
{


	public $timestamps = false;

	protected $table = 'admin_setting_groups';
	protected $primaryKey ='id';
}
