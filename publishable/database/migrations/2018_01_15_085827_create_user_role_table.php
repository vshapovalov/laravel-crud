<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_role', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();

            $table->foreign('user_id', 'user_role_user_id_foreign')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->bigInteger('role_id')->unsigned();

            $table->foreign('role_id', 'user_role_role_id_foreign')
                ->references('id')
                ->on('roles')
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
        Schema::table('user_role', function (Blueprint $table) {
            $table->dropForeign('user_role_user_id_foreign');
            $table->dropForeign('user_role_role_id_foreign');
        });

        Schema::dropIfExists('user_role');
    }
}
