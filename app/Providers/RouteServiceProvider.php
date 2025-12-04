<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Đăng ký alias middleware 'admin'
        Route::aliasMiddleware('admin', AdminMiddleware::class);

        parent::boot();
    }
}
