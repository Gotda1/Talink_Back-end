<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id()->unsigned();
			$table->bigInteger('order_id')->default(0);
			$table->bigInteger('acquirer_id')->unsigned();
			$table->bigInteger('seller_id')->unsigned();
            $table->string('invoice', 25)->unique();
            $table->string('catalogue', 40);
            $table->float('subtotal', 10, 2)->default(0);
			$table->float('taxes', 10, 2)->default(0);
			$table->float('total', 10, 2)->default(0);
			$table->string('location', 150)->nullable();
			$table->string('validity', 100);
			$table->string('warranty', 200);
			$table->double('advance_payment');
            $table->string('observations', 500)->nullable();
			$table->boolean('status')->default(0);
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
        Schema::dropIfExists('quotations');
    }
}
