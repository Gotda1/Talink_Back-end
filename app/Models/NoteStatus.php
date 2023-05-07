<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteStatus extends Model
{
    use HasFactory;

    protected $table = "note_status";
    protected $fillable = [
        "note_type", "name", "description", "color", "order", "status", "created_by"
    ];
}
