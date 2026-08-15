<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Complaint;
use App\Observers\ComplaintObserver;
use App\Models\MaintenanceReport;
use App\Observers\MaintenanceReportObserver;

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
        Complaint::observe(
            ComplaintObserver::class
        );

        MaintenanceReport::observe(
            MaintenanceReportObserver::class
        );
    }
}
