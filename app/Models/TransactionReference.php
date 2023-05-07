<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionReference extends Model
{
    use HasFactory;

    protected $table = "transactions_reference";

    protected $fillable = [
        "transaction_id", 
        "reference_id", 
        "subject_id", 
        "reference_type", 
        "subject_type", 
        "invoice"
    ];
    
    public $timestamps = false;

    public function transaction(){
        return $this->belongsTo(Transaction::class, "transaction_id", "id");
    }

    public function modelref(){
    	return $this->morphTo("reference");
    }
}
 