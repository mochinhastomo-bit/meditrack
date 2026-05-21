<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Jika user sudah login dan mencoba akses halaman guest (login, register),
        // arahkan ke dashboard sesuai role — hindari redirect loop ke '/'
        RedirectIfAuthenticated::redirectUsing(function () {
            $user = Auth::user();

            if (! $user) {
                return route('login');
            }

            return match($user->role) {
                'superadmin' => route('admin.dashboard'),
                'farmasi'    => route('farmasi.dashboard'),
                default      => route('login'),
            };
        });
    }
}
