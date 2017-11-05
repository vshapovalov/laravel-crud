<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('id');
	        $table->string('title', 191);
	        $table->string('code', 191)->nullable();
	        $table->string('url', 191)->nullable();
	        $table->integer('parent_id')->unsigned()->nullable();
	        $table->integer('order')->default(1);
	        $table->string('path', 191)->nullable();
	        $table->integer('level')->nullable();
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
        Schema::dropIfExists('menu_items');
    }
}
