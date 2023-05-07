<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivileges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privileges', function (Blueprint $table) {
            $table->string("code", 15)->primary();
            $table->string("name", 50);
            $table->string("description", 150)->nullable();
            $table->string('father', 15);
            $table->boolean('asignable')->default(0);
            $table->bigInteger('order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('privileges');        
        Schema::enableForeignKeyConstraints();
    }
}
