<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("units")->truncate();
        DB::table("units")->insert([[
            "abbreviation" => "U",
            "name"         => "Unidad",
            "status"       => 0
        ]]);
    }
}
