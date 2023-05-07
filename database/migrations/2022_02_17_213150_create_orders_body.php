<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersBody extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_body', function (Blueprint $table) {
            $table->bigInteger('order_id')->unsigned()->references('id')->on('orders');
			$table->bigInteger('product_id')->unsigned();
			$table->string('name', 250);
			$table->float('quantity', 16, 2);
			$table->float('quantity_surt', 16, 2)->default(0);
			$table->float('price_list', 16, 2);
			$table->float('discount', 10, 2)->default(0);
			$table->float('unit_price', 16, 2);
			$table->longText('observations')->nullable();
			$table->integer('order')->default(0);
			$table->boolean('status')->default(0);
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
        Schema::dropIfExists('orders_body');
    }
}
