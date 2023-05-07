<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogBalance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_balance', function (Blueprint $table) {
            $table->id()->unsigned();
			$table->bigInteger('transaction_id')->unsigned()->default(0);
			$table->bigInteger('entity_id')->unsigned();
            $table->string('entity', 40);
			$table->float('amount', 16, 2);
			$table->float('balance', 16, 2);
			$table->boolean('type');
            $table->string('invoice', 25)->nullable();
            $table->string('observations', 100);
			$table->bigInteger('created_by')->unsigned();
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_balance');
    }
}
