<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudScopesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crud_scopes', function (Blueprint $table) {
            $table->increments('id');
	        $table->integer('crud_form_id')->unsigned()->nullable();
	        $table->foreign('crud_form_id', 'crud_scopes_crud_form_id_foreign')
	              ->references('sur_id')
	              ->on('crud_forms')
	              ->onDelete('cascade');

	        $table->string('name');
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
	    Schema::create('crud_scopes', function (Blueprint $table) {
	        $table->dropForeign('crud_scopes_crud_form_id_foreign');
	    });

        Schema::dropIfExists('crud_scopes');
    }
}
