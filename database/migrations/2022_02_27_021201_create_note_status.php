<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_status', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->string('note_type', 40);
            $table->string('name', 50);
            $table->string('description', 100);
            $table->string('color', 15);
			$table->boolean('order');
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
        Schema::dropIfExists('note_status');
    }
}
