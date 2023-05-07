<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_attachments', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->bigInteger('note_id')->unsigned();
            $table->string('note_type',40);
			$table->string('attachment', 80);
			$table->string('description', 250)->nullable();
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
        Schema::dropIfExists('note_attachments');
    }
}