<?php

namespace App\Providers;

use App\Events\OrderCanceledEvent;
use App\Events\OrderCreatedEvent;
use App\Events\OrderDeletedEvent;
use App\Events\PaymentEvent;
use App\Events\RefundOrderEvent;
use App\Listeners\LogBalanceListener;
use App\Listeners\TransactionListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        OrderCreatedEvent::class => [
            LogBalanceListener::class
        ],

        PaymentEvent::class => [
            LogBalanceListener::class
        ],

        RefundOrderEvent::class => [
            LogBalanceListener::class
        ],

        OrderCanceledEvent::class => [
            LogBalanceListener::class
        ],

        OrderDeletedEvent::class => [
            LogBalanceListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(
            PodcastProcessed::class,
            [SendPodcastNotification::class, 'handle']
        );
    }
}
