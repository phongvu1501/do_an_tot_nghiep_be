<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Models\Review;
use App\Policies\ReservationPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapping giữa Model và Policy tương ứng
     */
    protected $policies = [
        Reservation::class => ReservationPolicy::class,
        Review::class      => ReviewPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies(); // Laravel < 11, nhưng vẫn hoạt động nếu bạn muốn giữ
    }
}
