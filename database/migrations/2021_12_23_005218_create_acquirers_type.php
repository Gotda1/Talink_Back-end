<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcquirersType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acquirers_type', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->string('code', 15)->unique();
			$table->string('name', 50);
			$table->string('description', 250);
			$table->bigInteger('created_by')->unsigned();
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
        Schema::dropIfExists('acquirers_type');
    }
}
