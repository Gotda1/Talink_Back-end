<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('account_id')->unsigned();
			$table->bigInteger('concept_id')->unsigned()->default(0);
            $table->string("payment_method_code", 15);
			$table->float('amount', 16, 2);
            $table->string('observations', 100)->nullable();
            $table->dateTime('created_at');
			$table->bigInteger('created_by')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
