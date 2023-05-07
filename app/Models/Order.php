<?php

namespace App\Models;

use App\Events\OrderCanceledEvent;
use App\Events\OrderCreatedEvent;
use App\Events\PaymentEvent;
use App\Events\RefundOrderEvent;
use App\Traits\HelperTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    public static $folder           = "orders";
    public static $concept_charge   = 1;
    public static $concept_payment  = 2;
    public static $concept_refund   = 3;
    public static $concept_canceled = 4;

    use HasFactory, HelperTrait, SoftDeletes;


    protected $fillable = [
        "client_id",
        "seller_id",
        "payment_form",
        "invoice",
        "subtotal",
        "taxes",
        "total",
        "payed",
        "location",
        "delivery_time",
        "observations",
        "warranty",
        "advance_payment",
        "status",
        "contratado_at",
        "created_by",
        "deleted_by"
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "created_by", "id");
    }

    public function seller()
    {
        return $this->belongsTo(User::class, "seller_id", "id");
    }

    public function client()
    {
        return $this->belongsTo(Client::class, "client_id", "id");
    }

    public function order_body()
    {
        return $this->hasMany(OrderBody::class, "order_id", "id")->orderBy("order", "asc");
    }

    public function attachments_img()
    {
        return $this->morphMany(NoteAttachment::class, 'attachmentable', 'note_type', 'note_id');
    }

    public function note_observations()
    {
        return $this->morphMany(NoteObservation::class, 'observationable','note_type','note_id');
    }

    public function t_reference()
    {
        return $this->morphMany(TransactionReference::class, "modelref", "reference_type", "reference_id", "id");
    }

    /**
     * Make new invoice code
     *
     * @param User|null $seller
     * @return string
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public static function makeNewInvoice($seller)
    {
        $last = Order::withTrashed()
                        ->select(DB::raw("CAST(SUBSTRING_INDEX(invoice,'-', -1) AS UNSIGNED INTEGER) + 1 as new_invoice"))
                        ->whereRaw("SUBSTRING_INDEX(invoice,'-', 1) = '{$seller->code}'")
                        ->pluck('new_invoice')
                        ->last() ?? 1;

        return $seller->code . "-"  . str_pad($last, 7, "0", STR_PAD_LEFT);
    }

    public function setAmounts()
    {
        $this->subtotal = round(($this->order_body->reduce(function ($carry, $item)
        {
            return $carry + ($item["unit_price"] * $item["quantity"]);
        }) ?? 0), 2);

        $this->taxes = round(($this->taxes > 0 ? $this->calculateTaxes($this->subtotal) : 0), 2);
        $this->total = $this->subtotal + $this->taxes;
        $this->save();
    }


    public function validatePayment($amount)
    {
        $payed = round($this->payed + $amount, 2);

        if ($payed > $this->total)
        {
            $reamainig = round($this->total - $this->payed, 2);
            return [false, $reamainig];
        }

        return [true, $payed];
    }

    public function addChargeTransaction($amount, $account_id = 1, $payment_method_code = "UNDF", $observations = "")
    {
        try
        {
            DB::beginTransaction();

            $transaction = Transaction::create([
                "account_id"          => $account_id,
                "concept_id"          => Order::$concept_charge,
                "payment_method_code" => $payment_method_code,
                "amount"              => $amount,
                "observations"        => $observations
            ]);

            TransactionReference::create([
                "transaction_id" => $transaction->id,
                "reference_id"   => $this->id,
                "subject_id"     => $this->client_id,
                "reference_type" => Order::class,
                "subject_type"   => Client::class,
                "invoice"        => $this->invoice
            ]);

            #   Client balance
            $client = Client::find($this->client_id);
            $client->decrementBalance($amount);

            DB::commit();

            event(new OrderCreatedEvent($transaction));

            return [true, $transaction];
        }
        catch (\Exception $e)
        {
            report($e);
            DB::rollback();

            return [false, $e];
        }
    }

    public function addPaymentTransaction($amount, $account_id = 1, $payment_method_code = "UNDF", $observations = "")
    {
        try
        {
            DB::beginTransaction();

            $transaction = Transaction::create([
                "account_id"          => $account_id,
                "concept_id"          => Order::$concept_payment,
                "payment_method_code" => $payment_method_code,
                "amount"              => $amount,
                "observations"        => $observations
            ]);

            TransactionReference::create([
                "transaction_id" => $transaction->id,
                "reference_id"   => $this->id,
                "subject_id"     => $this->client_id,
                "reference_type" => Order::class,
                "subject_type"   => Client::class,
                "invoice"        => $this->invoice
            ]);

            #   Order payed
            $this->payed += round($amount, 2);
            $this->save();

            #   Client balance
            $client = Client::find($this->client_id);
            $client->incrementBalance($amount);

            #   Account balance
            $account = Account::find($account_id);
            $account->incrementBalance($amount);

            DB::commit();

            event(new PaymentEvent($transaction));

            return [true, $transaction];
        }
        catch (\Exception $e)
        {
            report($e);
            DB::rollback();

            return [false, $e];
        }
    }

    public function addRefundTransaction($amount, $account_id = 1, $payment_method_code = "UNDF", $observations = "")
    {
        try
        {
            DB::beginTransaction();

            $transaction = Transaction::create([
                "account_id"          => $account_id,
                "concept_id"          => Order::$concept_refund,
                "payment_method_code" => $payment_method_code,
                "amount"              => $amount,
                "observations"        => $observations
            ]);

            TransactionReference::create([
                "transaction_id" => $transaction->id,
                "reference_id"   => $this->id,
                "subject_id"     => $this->client_id,
                "reference_type" => Order::class,
                "subject_type"   => Client::class,
                "invoice"        => $this->invoice
            ]);

            #   Order payed
            $this->payed -= round($amount, 2);
            $this->save();

            #   Client balance
            $client = Client::find($this->client_id);
            $client->decrementBalance($amount);

            #   Account balance
            $account = Account::find($account_id);
            $account->decrementBalance($amount);

            DB::commit();

            event(new RefundOrderEvent($transaction));

            return [true, $transaction];
        }
        catch (\Exception $e)
        {
            report($e);
            DB::rollback();

            return [false, $e];
        }
    }

    public function addCanceledTransaction($amount, $account_id = 1, $payment_method_code = "UNDF", $observations = "")
    {
        try
        {
            DB::beginTransaction();

            $transaction = Transaction::create([
                "account_id"          => $account_id,
                "concept_id"          => Order::$concept_canceled,
                "payment_method_code" => $payment_method_code,
                "amount"              => $amount,
                "observations"        => $observations
            ]);

            TransactionReference::create([
                "transaction_id" => $transaction->id,
                "reference_id"   => $this->id,
                "subject_id"     => $this->client_id,
                "reference_type" => Order::class,
                "subject_type"   => Client::class,
                "invoice"        => $this->invoice
            ]);

            #   Client balance
            $client = Client::find($this->client_id);
            $client->incrementBalance($amount);

            DB::commit();

            event(new OrderCanceledEvent($transaction));

            return [true, $transaction];
        }
        catch (\Exception $e)
        {
            report($e);
            DB::rollback();

            return [false, $e];
        }
    }

    public function termsAndConditions()
    {
        $terms     =  Config::get("app.TERMS_CONDITIONS");
        $terms     = str_replace("{WARRANTY}", $this->warranty, $terms);
        $terms     = str_replace("{ADVANCE_PAYMENT}", $this->advance_payment, $terms);
        return $terms;
    }
}
