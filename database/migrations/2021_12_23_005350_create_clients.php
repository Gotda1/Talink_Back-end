<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('acquirer_type_code', 15);
            $table->string('code', 15)->unique();
			$table->string('name', 150);
			$table->string('official_name', 250)->nullable();
			$table->string('description', 500)->nullable();
            $table->string('rfc', 20)->nullable();
            $table->string('email', 80);
            $table->string('phone', 20);
            $table->string('location', 50)->nullable();
            $table->string('address', 300)->nullable();
			$table->float('balance', 10, 2)->default(0);
            $table->boolean('status');
			$table->bigInteger('created_by')->unsigned(); 
			$table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('clients');
    }
}