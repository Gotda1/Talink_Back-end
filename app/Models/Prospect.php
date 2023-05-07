<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Prospect extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = "prospects";

    protected $fillable = [
        "user_id", 
        "acquirer_type_code", 
        "name",  
        "rfc", 
        "address", 
        "location", 
        "email", 
        "phone", 
        "status", 
        "created_by"
    ];

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
}
