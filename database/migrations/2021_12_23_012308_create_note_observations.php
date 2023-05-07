<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteObservations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_observations', function (Blueprint $table) {
            $table->bigInteger('note_id')->unsigned();
            $table->bigInteger('note_status_id')->unsigned();
            $table->string('note_type',40);
            $table->string('observations', 500);
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
        Schema::dropIfExists('note_observations');
    }
}