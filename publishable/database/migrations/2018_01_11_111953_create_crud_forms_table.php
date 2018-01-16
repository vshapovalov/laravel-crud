<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crud_forms', function (Blueprint $table) {
	        $table->increments('sur_id');
	        $table->string('name', 191);
	        $table->string('code', 191);
	        $table->string('model', 191);
	        $table->string('id', 191);
	        $table->string('display', 191);
	        $table->string('type', 191)->default('list');
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
        Schema::dropIfExists('crud_forms');
    }
}
