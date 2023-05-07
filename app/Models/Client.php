<?php

namespace App\Models;

use App\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use HasFactory, HelperTrait, SoftDeletes;

    protected $fillable = [ 
        "user_id", 
        "acquirer_type_code", 
        "code",  
        "name", 
        "official_name", 
        "rfc", 
        "email", 
        "phone", 
        "location", 
        "address",
        "balance", 
        "status", 
        "created_by" 
    ];

    /**
     * Make new cliente code
     *
     * @param string $acquirer_type_code
     * @return string $new_code
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    static public function makeNewCode( $acquirer_type_code ){
        $last = Client::select(DB::raw("CAST(SUBSTRING_INDEX(code,'-', -1) AS UNSIGNED INTEGER) + 1 as new_code"))
                        ->withTrashed()
                        ->where("acquirer_type_code", $acquirer_type_code)
                        ->pluck('new_code')
                        ->last() ?? 1;

        return $acquirer_type_code . "-"  . str_pad($last, 7, "0", STR_PAD_LEFT);
    }

    public function acquirer_type(){
        return $this->belongsTo(AcquireType::class, "acquirer_type_code", "code");
    }

    public function user(){
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function quotations()
    {
        return $this->morphMany(Quotation::class, "acquirer","catalogue", "acquirer_id");
    }

    public function orders()
    {
        return $this->hasMany(Order::class, "client_id","id");
    }

    public function incrementBalance($amount){
        $this->balance += round($amount, 2); 
        $this->save();
    }

    public function decrementBalance($amount){
        $this->balance -= round($amount, 2); 
        $this->save();
    }
}