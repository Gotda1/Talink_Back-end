<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcquirersTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("acquirers_type")->truncate();
        DB::table("acquirers_type")->insert([
            "code"        => "BUSS",
            "name"        => "Empresa",
            "description" => "Empresa", 
            "created_by"  => 1
        ]);
    }
}
