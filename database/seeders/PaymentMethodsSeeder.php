<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("payment_methods")->truncate();
        DB::table("payment_methods")->insert([
            [
                "code"        => "EFCT",
                "name"        => "Efectivo",
                "description" => "Efectivo",
                "status"      => 1
            ],
            [
                "code"        => "TRJT",
                "name"        => "Tarjeta",
                "description" => "Tarjeta",
                "status"      => 1
            ],
            [
                "code"        => "TRNS",
                "name"        => "Transferencia",
                "description" => "Transferencia",
                "status"      => 1
            ],
            [
                "code"        => "DPTO",
                "name"        => "Depósito",
                "description" => "Depósito",
                "status"      => 1
            ],
        ]);
    }
}
