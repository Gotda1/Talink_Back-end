<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = "transactions";

    protected $fillable = [ 
        "account_id", 
        "concept_id", 
        "payment_method_code", 
        "amount", 
        "observations",
        "created_at"  
    ];
    
    public $timestamps = false;
    
    public function concept(){
        return $this->belongsTo(Concept::class, "concept_id", "id");
    }

    public function account(){
        return $this->belongsTo(Account::class, "account_id", "id");
    }

    public function reference(){
        return $this->hasOne(TransactionReference::class, "transaction_id", "id");
    }
}
