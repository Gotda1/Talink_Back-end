<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->unsigned();
			$table->bigInteger('client_id')->unsigned();
			$table->bigInteger('seller_id')->unsigned();
            $table->string('invoice', 25)->unique();
            $table->float('subtotal', 10, 2)->default(0);
			$table->float('taxes', 10, 2)->default(0);
			$table->float('total', 10, 2)->default(0);
            $table->float('payed', 10, 2)->default(0);
			$table->string('location', 150)->nullable();
			$table->string('delivery_time', 100)->nullable();
            $table->string('observations', 500)->nullable();
			$table->string('warranty', 200);
			$table->double('advance_payment');
			$table->boolean('candidates')->default(0);
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
        Schema::dropIfExists('orders');
    }
}
