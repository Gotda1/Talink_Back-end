<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->string("code", 15)->unique();
            $table->string("role_code", 15);
            $table->string("email", 80);
            $table->string("name", 200);
            $table->date("birthday");
            $table->string("description", 250)->nullable();
            $table->string("phone", 20)->nullable();
            $table->string("password", 100);
            $table->boolean('status');
            $table->bigInteger("created_by")->unsigned();
            $table->timestamps();
            $table->foreign('role_code')->references('code')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
