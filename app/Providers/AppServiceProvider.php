<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\LogBalance;
use App\Models\NoteAttachment;
use App\Models\NoteObservation;
use App\Models\Order;
use App\Models\OrderBody;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\QuotationBody;
use App\Models\Transaction;
use App\Models\TransactionReference;
use App\Models\User;
use App\Observers\ClientObserver;
use App\Observers\LogBalanceObserver;
use App\Observers\NoteAttachmentObserver;
use App\Observers\NoteObservationObserver;
use App\Observers\OrderBodyObserver;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use App\Observers\ProspectObserver;
use App\Observers\QuotationBodyObserver;
use App\Observers\QuotationObserver;
use App\Observers\TransactionObserver;
use App\Observers\TransactionReferenceObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Prospect::observe(ProspectObserver::class);
        Client::observe(ClientObserver::class);
        Product::observe(ProductObserver::class);
        Quotation::observe(QuotationObserver::class);
        QuotationBody::observe(QuotationBodyObserver::class);
        Order::observe(OrderObserver::class);
        OrderBody::observe(OrderBodyObserver::class);
        NoteAttachment::observe(NoteAttachmentObserver::class);
        NoteObservation::observe(NoteObservationObserver::class);
        Transaction::observe(TransactionObserver::class);
        TransactionReference::observe(TransactionReferenceObserver::class);
        LogBalance::observe(LogBalanceObserver::class);
    }
}
