<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteObservation extends Model
{
    use HasFactory;

    protected $fillable = [
        "note_id", 
        "note_status_id", 
        "note_type", 
        "observations", 
        "created_by"
    ];

    public function observationable()
    {
    	return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, "created_by", "id");
    }

    public function note_status(){
        return $this->belongsTo(NoteStatus::class, "note_status_id", "id");
    }
}
