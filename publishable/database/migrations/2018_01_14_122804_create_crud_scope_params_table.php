<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudScopeParamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crud_scope_params', function (Blueprint $table) {
            $table->increments('id');
	        $table->integer('crud_scope_id')->unsigned()->nullable();
	        $table->foreign('crud_scope_id', 'foreign_crud_scope_params_crud_scope_id')
	              ->references('id')
	              ->on('crud_scopes')->onDelete('cascade');
	        $table->string('name', 191);
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
	    Schema::table('crud_scope_params', function (Blueprint $table) {
	        $table->dropForeign('foreign_crud_scope_params_crud_scope_id');
	    });

        Schema::dropIfExists('crud_scope_params');
    }
}
