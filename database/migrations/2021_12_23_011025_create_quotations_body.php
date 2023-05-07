<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsBody extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations_body', function (Blueprint $table) {
            $table->bigInteger('quotation_id')->unsigned()->references('id')->on('quotations');
			$table->bigInteger('product_id')->unsigned();
			$table->string('name', 250);
			$table->float('quantity', 16, 2);
			$table->float('price_list', 16, 2); 
			$table->float('discount', 10, 0)->default(0);
			$table->float('unit_price', 16, 2);
			$table->integer('order')->default(0);
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
        Schema::dropIfExists('quotations_body');
    }
}
