<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Client;
use App\Models\LogBalance;
use App\Models\NoteAttachment;
use App\Models\Order;
use App\Models\OrderBody;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\QuotationBody;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FakesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->users();

        $user = User::with("role")->find(1);
        Auth::login($user);

        Log::info(Auth::user());

        LogBalance::truncate();

        Quotation::truncate();
        QuotationBody::truncate();

        Order::truncate();
        OrderBody::truncate();

        NoteAttachment::truncate();

        Transaction::truncate();
        
        $this->prospects();
        $this->clients();
        $this->product_categories();
        $this->products();
        $this->accounts();
        $this->concepts();

        
        Auth::logout();
    }

    private function product_categories(){
        ProductCategory::truncate();

        ProductCategory::create([
            "name"        => "Servicios", 
            "description" => "Producto tipo servicios", 
            "status"      => 1, 
            "created_by"  => 0,
            "created_at"  => Carbon::now(),
            "updated_at"  => Carbon::now()
        ]);
    }

    private function users(){
        User::truncate();

        User::create([
            "code"        => "MULLOA",
            "role_code"   => "ADMN",
            "email"       => "guadalupe.ulloa@outlook.com",
            "name"        => "Guadalupe Ulloa",
            "description" => "Usuario de prueba",
            "phone"       => "3320629615",
            "password"    => "123456",
            "birthday"    => "1993-06-07",
            "status"      => 1,
            "created_by"  => 0,
            "created_at"  => Carbon::now(),
            "updated_at"  => Carbon::now()
        ]);
    }

    private function prospects(){
        Prospect::truncate();
        Prospect::factory(6)->create();
    }
    
    private function clients(){
        Client::truncate();
        Client::factory(6)->create();
    }

    private function products(){
        Product::truncate();
        Product::factory(20)->create();
    }

    private function accounts(){
        Account::truncate();
        Account::create([
            "name"        => "Principal",
            "description" => "Cuenta principal",
            "balance"     => 0,
            "created_by"  => 0
        ]);
    }

    private function concepts(){
        DB::table("concepts")->truncate(); 
        DB::table("concepts")->insert([
            [
                "name"        => "Cargo",
                "description" => "Cargo",
                "type"        => 0,
                "created_by"  => 0
            ],
            [
                "name"        => "Pago pedido",
                "description" => "Pago pedido",
                "type"        => 1,
                "created_by"  => 0
            ],
            [
                "name"        => "Reembolso",
                "description" => "Reembolso",
                "type"        => 0,
                "created_by"  => 0
            ],
            [
                "name"        => "Debit Canceled",
                "description" => "Debit Canceled",
                "type"        => 1,
                "created_by"  => 0
            ]
        ]);
    }
}
