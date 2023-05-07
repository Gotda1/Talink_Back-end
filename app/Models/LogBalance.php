<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogBalance extends Model
{
    use HasFactory;

    protected $table = "log_balance";

    public $timestamps = false;

    protected $fillable = [
        "transaction_id",
        "entity_id",
        "entity",
        "amount",
        "balance",
        "type",
        "invoice",
        "observations",
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }
}
