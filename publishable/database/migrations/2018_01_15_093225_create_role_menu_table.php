<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_menu', function (Blueprint $table) {
	        $table->integer('role_id')->unsigned();

	        $table->foreign('role_id', 'role_crud_role_id_foreign')
	              ->references('id')
	              ->on('roles')
	              ->onDelete('cascade');

	        $table->integer('menu_id')->unsigned();

	        $table->foreign('menu_id', 'role_crud_menu_id_foreign')
	              ->references('id')
	              ->on('crud_menus')
	              ->onDelete('cascade');

	        $table->json('meta')->nullable();

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
	    Schema::table('role_menu', function (Blueprint $table) {
	    	$table->dropForeign('role_crud_role_id_foreign');
		    $table->dropForeign('role_crud_menu_id_foreign');
	    });

        Schema::dropIfExists('role_menu');
    }
}
