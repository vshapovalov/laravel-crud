<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crud_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
	        $table->bigInteger('crud_form_id')->unsigned()->nullable();
	        $table->string('name', 191);
	        $table->string('caption', 191);
	        $table->string('type', 191);
	        $table->string('visibility', 191)->nullable();
	        $table->string('by_default', 191)->nullable();
	        $table->boolean('json')->default(false);
	        $table->boolean('readonly')->default(false);
	        $table->string('description', 191)->nullable();
	        $table->string('tab', 191);
	        $table->string('validation', 191)->nullable();
	        $table->text('additional')->nullable();
	        $table->integer('order')->default(0);
	        $table->integer('columns')->default(12);
	        $table->bigInteger('crud_relation_id')->unsigned()->nullable();
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
        Schema::dropIfExists('crud_fields');
    }
}
