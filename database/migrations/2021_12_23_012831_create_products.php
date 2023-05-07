<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unit_id')->unsigned();
            $table->bigInteger('product_category_id')->unsigned();
            $table->string('product_type_code',15);
            $table->string('code', 15)->unique();
			$table->string('name', 250);
			$table->string('description', 500)->nullable();
			$table->double('price_list', 10,2);
			$table->boolean('flec_price')->default(0);
            $table->boolean('status');
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
        Schema::dropIfExists('products');
    }
}
