<?php

namespace App\Models;

use App\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Quotation extends Model
{
    use HasFactory, HelperTrait, SoftDeletes;

    public static $folder = "quotations";

    protected $fillable = [
        "acquirer_id", 
        "seller_id", 
        "order_id", 
        "payment_form", 
        "invoice",
        "catalogue", 
        "subtotal", 
        "taxes",  
        "total", 
        "location", 
        "delivery_time", 
        "validity", 
        "observations", 
        "warranty",
        "advance_payment",
        "status",
        "created_by",
        "deleted_by"
    ]; 

    public function client(){
        return $this->belongsTo(Client::class, "acquirer_id", "id");
    }

    public function prospect(){
        return $this->belongsTo(Prospect::class, "acquirer_id", "id");
    }

    public function acquirer()
    {
    	return $this->morphTo("acquirer", "catalogue");
    }

    public function user(){
        return $this->belongsTo(User::class, "created_by", "id");
    }

    public function seller(){
        return $this->belongsTo(User::class, "seller_id", "id");
    }
    // public function formaPago(){
    //     return $this->belongsTo(FormasPago::class, "forma_pago_clave", "clave");
    // }

    public function quot_body(){
        return $this->hasMany(QuotationBody::class, "quotation_id", "id")->orderBy("order");
    }
    
    public function attachments_img(){
        return $this->morphMany(NoteAttachment::class, 'attachmentable','note_type','note_id')->orderBy("created_at");
    }

    public function note_observations(){
        return $this->morphMany(NoteObservation::class, 'observationable','note_type','note_id');
    }

    // public function observacionesSeg(){
    //     return $this->hasMany(CotizacionObservacion::class, "cotizacion_id", "id");
    // }

    public function order()
    {
        return $this->belongsTo(Order::class, "order_id", "id");
    }

    /**
     * Make new invoice code
     *
     * @param User|null $seller
     * @return string
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public static function makeNewInvoice( $seller ){
        $last = Quotation::select(DB::raw("CAST(SUBSTRING_INDEX(invoice,'-', -1) AS UNSIGNED INTEGER) + 1 as new_invoice"))
                                ->withTrashed()
                                ->where("seller_id", $seller->id)
                                ->pluck('new_invoice')
                                ->last() ?? 1;

        return $seller->code . "-"  . str_pad($last, 7, "0", STR_PAD_LEFT);
    }

    public function setAmounts(){
        $this->subtotal = round(($this->quot_body->reduce(function ($carry, $item) {
            return $carry + ($item["unit_price"] * $item["quantity"]);
        }) ?? 0), 2);

        $this->taxes = round( ($this->taxes > 0 ? $this->calculateTaxes($this->subtotal) : 0), 2 );
        $this->total = $this->subtotal + $this->taxes;
        $this->save();
    }

    public function termsAndConditions(){
        $terms     =  Config::get("app.TERMS_CONDITIONS");
        $terms     = str_replace("{WARRANTY}", $this->warranty, $terms);
        $terms     = str_replace("{ADVANCE_PAYMENT}", $this->advance_payment, $terms);
        return $terms;
    }
}
