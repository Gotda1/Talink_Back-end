<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsReference extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions_reference', function (Blueprint $table) {
			$table->bigInteger('transaction_id')->unsigned();
			$table->bigInteger('reference_id')->unsigned()->default(0);
			$table->bigInteger('subject_id')->unsigned()->default(0);
            $table->string('reference_type', 40);
            $table->string('subject_type', 40);
            $table->string('invoice', 25);
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
        Schema::dropIfExists('transactions_reference');
    }
}
