<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Professional;
use App\Policies\AppointmentPolicy;
use App\Policies\ProfessionalPolicy;
use Illuminate\Pagination\Paginator;
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
        // Use Tailwind-compatible pagination
        Paginator::defaultView('pagination::tailwind');
        Paginator::defaultSimpleView('pagination::simple-tailwind');

        // Register policies
        \Illuminate\Support\Facades\Gate::policy(Appointment::class, AppointmentPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(Professional::class, ProfessionalPolicy::class);
    }
}
