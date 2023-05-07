<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('acquirer_type_code', 15);
			$table->string('name', 150);
			$table->string('description', 250)->nullable();
			$table->string('rfc', 20)->nullable();
			$table->string('address', 300)->nullable();
			$table->string('location', 50)->nullable();
			$table->string('email', 80);
			$table->string('phone', 20);
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
        Schema::dropIfExists('prospects');
    }
}
