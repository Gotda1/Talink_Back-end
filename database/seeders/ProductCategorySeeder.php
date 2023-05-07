<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("product_categories")->truncate();
        DB::table("product_categories")->insert([[
            "name"        => "Servicio",
            "description" => "Servicio",
            "status"      => 0,
            "created_by"  => 0
        ]]);
    }
}
