<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        try {
            Schema::disableForeignKeyConstraints();
            // $this->call( ProductCategorySeeder::class );
            // $this->call( ProductTypeSeeder::class );
            // $this->call( UnitSeeder::class );
            // $this->call( RolesSeeder::class );
            $this->call( PrivilegesSeeder::class );  
            // $this->call( AcquirersTypeSeeder::class);
            // $this->call( NoteStatusSeeder::class);
            // $this->call( FakesSeeder::class );  
            // $this->call( PaymentMethodsSeeder::class );  
            
            Schema::enableForeignKeyConstraints();
            
        } catch (\Throwable $th) {
            report($th);
        }
    }
}
