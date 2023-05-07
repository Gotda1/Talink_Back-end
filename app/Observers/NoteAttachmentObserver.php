<?php

namespace App\Observers;

use App\Models\NoteAttachment;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NoteAttachmentObserver
{
    use HelperTrait;
    
    /**
     * Handle the Quotation "creating" event.
     *
     * @param  \App\Models\NoteAttachment  $orderBody
     * @return void
     */
    public function creating(NoteAttachment $noteAttachment)
    {
        $model = app($noteAttachment->note_type);
        $note  = $model->find($noteAttachment["note_id"]);
        $noteAttachment->created_by = Auth::id();
        if(Str::contains($noteAttachment->attachment, "base64,"))
            $noteAttachment->attachment = $this->saveImageB64($model::$folder, $note->invoice, $noteAttachment->attachment);
    }

    /**
     * Handle the NoteAttachment "deleted" event.
     *
     * @param  \App\Models\NoteAttachment  $noteAttachment
     * @return void
     */
    public function deleted(NoteAttachment $noteAttachment)
    {
        Log::info($noteAttachment);
        $model = app($noteAttachment->note_type);
        $note  = $model->find($noteAttachment["note_id"]);
        Storage::disk("public")->delete($model::$folder . "/$note->invoice/$noteAttachment->attachment");
    }
}
