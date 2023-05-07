<?php

namespace App\Common;

use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Prospect;
use App\Models\User;
use App\Traits\HelperTrait;

class Catalogs {
    use HelperTrait;

    /**
     * Get users sellers
     *
     * @return void
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public static function getSellers(){
        return User::select("id","code","name")
        ->whereIn("role_code", ["VTAS", "ADMN"])
        ->where("status", 1)
        ->orderBy("name", "asc")
        ->get();
    }

    public static function getProspects($user_id = null){
        return Prospect::select("id", "name")
                            ->where("status", 1)
                            ->when($user_id, function($query) use($user_id){
                                $query->whereIn("user_id", [0, $user_id]);
                            })->orderBy("name", "asc")
                            ->get();
    }

    public static function getClients($user_id = null){
        return Client::select("id", "name")
                            ->where("status", 1)
                            ->when($user_id, function($query) use($user_id){
                                $query->whereIn("user_id", [0, $user_id]);
                            })->orderBy("name", "asc")
                            ->get();
    }

    public static function getPaymentMethods(){
        return PaymentMethod::where("status", 1)
                            ->get();
    }
}
