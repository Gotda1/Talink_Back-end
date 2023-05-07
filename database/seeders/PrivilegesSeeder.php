<?php

namespace Database\Seeders;

use App\Imports\PrivilegesImports;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class PrivilegesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   Schema::disableForeignKeyConstraints();
        DB::table("rel_role_privilege")->truncate();
        DB::table("privileges")->truncate();
        Schema::enableForeignKeyConstraints();

        // PRIVILEGES
        $path = storage_path() . "/privileges.xlsx";
 
        Excel::import(new PrivilegesImports, $path);
    }
}
