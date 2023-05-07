<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("roles")->truncate();
        DB::table("roles")->insert([[
                "code"        => "ADMN",
                "name"        => "Administrador",
                "description" => "Administrador",
            ], [
                "code"        => "VTAS",
                "name"        => "Reclutador",
                "description" => "Reclutador"
            ],[
                "code"        => "CONT",
                "name"        => "Contabilidad",
                "description" => "Contabilidad"
            ]
        ]); 
    }
}
