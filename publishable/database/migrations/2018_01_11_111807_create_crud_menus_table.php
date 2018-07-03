<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crud_menus', function (Blueprint $table) {
            $table->increments('id');
	        $table->string('name', 191);
	        $table->string('caption', 191);
            $table->string('icon', 191)->nullable();
	        $table->string('action', 191)->nullable();
	        $table->boolean('default')->default(false);
	        $table->integer('parent_id')->nullable();
	        $table->integer('order')->default(0);
	        $table->string('status')->default('enabled')->nullable();
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
        Schema::dropIfExists('crud_menus');
    }
}
