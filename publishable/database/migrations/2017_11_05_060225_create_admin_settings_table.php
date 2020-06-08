<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
	        $table->string('key', 191);
	        $table->bigInteger('admin_setting_group_id')
	              ->unsigned()
	              ->nullable();

	        $table->foreign('admin_setting_group_id')
	              ->references('id')
	              ->on('admin_setting_groups');

	        $table->integer('crud_field_type_id')
	              ->unsigned()
	              ->nullable();

	        $table->foreign('crud_field_type_id')
	              ->references('id')
	              ->on('crud_field_types');

	        $table->string('value', 191)->nullable();
	        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_settings');
    }
}
