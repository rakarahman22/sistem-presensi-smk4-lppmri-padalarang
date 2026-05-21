<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

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
        //
        // Cek apakah yang login adalah Admin
        Gate::define('is-admin', function () {
            return Auth::guard('admin')->check();
        });

        // Cek apakah yang login adalah Guru
        Gate::define('is-guru', function () {
            return Auth::guard('guru')->check();
        });

        // Cek apakah yang login adalah Wali Murid
        Gate::define('is-wali', function () {
            return Auth::guard('wali')->check();
        });

        // Cek apakah yang login adalah Siswa
        Gate::define('is-siswa', function () {
            return Auth::guard('siswa')->check();
        });
    }
}
