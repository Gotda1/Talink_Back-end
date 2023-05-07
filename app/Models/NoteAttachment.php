<?php

namespace App\Models;

use App\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NoteAttachment extends Model
{
    use HasFactory, HelperTrait;

    protected $table = "note_attachments";
    
    protected $fillable = [
        "note_id",
        "note_type",
        "attachment",
        "description",
        "created_by",
    ];

    public function noteable() 
    {
        return $this->morphTo("noteable", "note_type");
    }
}
