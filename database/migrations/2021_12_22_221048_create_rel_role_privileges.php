<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelRolePrivileges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rel_role_privilege', function (Blueprint $table) {
            $table->string("role_code", 15);
            $table->string("privilege_code", 15);
            $table->foreign('role_code')->references('code')->on('roles');
            $table->foreign('privilege_code')->references('code')->on('privileges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rel_role_privileges');
    }
}
