<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleCrudFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_crud_form', function (Blueprint $table) {
	        $table->integer('role_id')->unsigned();

	        $table->foreign('role_id', 'role_crud_form_role_id_foreign')
	              ->references('id')
	              ->on('roles')
	              ->onDelete('cascade');

	        $table->integer('crud_form_id')->unsigned();

            $table->integer('select')->default(1);
	        $table->integer('add')->default(1);
	        $table->integer('edit')->default(1);
	        $table->integer('delete')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('role_crud_form', function (Blueprint $table) {
	    	$table->dropForeign('role_crud_form_role_id_foreign');
	    });

        Schema::dropIfExists('role_crud_form');
    }
}
