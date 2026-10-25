<?php

namespace App\Providers;

use App\Models\Complaint;
use App\Models\MaintenanceReport;
use App\Models\ServiceAnnouncement;
use App\Observers\ComplaintObserver;
use App\Observers\MaintenanceReportObserver;
use App\Services\MailSettingsService;
use Illuminate\Support\Facades\View;
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
        /*
        |--------------------------------------------------------------------------
        | Register Model Observers
        |--------------------------------------------------------------------------
        */

        Complaint::observe(
            ComplaintObserver::class
        );

        MaintenanceReport::observe(
            MaintenanceReportObserver::class
        );

        /*
        |--------------------------------------------------------------------------
        | Apply Database-Backed SMTP Settings
        |--------------------------------------------------------------------------
        |
        | This applies the saved SMTP configuration so the application can
        | use it for emails, including OTP verification and password resets.
        |
        */

        try {
            app(MailSettingsService::class)->apply();
        } catch (\Throwable $exception) {
            /*
             * Keep the application running if the mail settings table
             * has not been created yet or the database settings fail.
             */
            report($exception);
        }

        /*
        |--------------------------------------------------------------------------
        | Consumer Announcement Navbar
        |--------------------------------------------------------------------------
        */

        View::composer(
            'consumer.layouts.navbar',
            function ($view) {
                $newAnnouncementCount = 0;

                if (
                    auth()->check() &&
                    auth()->user()->hasRole('Consumer')
                ) {
                    $consumer = auth()->user()->consumer;

                    if ($consumer) {
                        $newAnnouncementCount =
                            ServiceAnnouncement::published()
                                ->whereDoesntHave(
                                    'reads',
                                    function ($query) use ($consumer) {
                                        $query->where(
                                            'consumer_id',
                                            $consumer->id
                                        );
                                    }
                                )
                                ->count();
                    }
                }

                $view->with(
                    'newAnnouncementCount',
                    $newAnnouncementCount
                );
            }
        );
    }
}