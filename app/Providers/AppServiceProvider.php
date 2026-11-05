<?php

namespace App\Providers;

use App\Mail\Transport\BrevoTransport;
use App\Models\Complaint;
use App\Models\MaintenanceReport;
use App\Models\ServiceAnnouncement;
use App\Observers\ComplaintObserver;
use App\Observers\MaintenanceReportObserver;
use App\Services\MailSettingsService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
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
        // Force HTTPS URLs in production.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        /*
        |--------------------------------------------------------------------------
        | Register Model Observers
        |--------------------------------------------------------------------------
        */

        Complaint::observe(ComplaintObserver::class);

        MaintenanceReport::observe(
            MaintenanceReportObserver::class
        );

        /*
        |--------------------------------------------------------------------------
        | Register Brevo HTTPS API Mail Transport
        |--------------------------------------------------------------------------
        */

        Mail::extend('brevo', function (array $config = []) {
            return new BrevoTransport(
                $config['key'] ?? config('services.brevo.key', '')
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Apply Database-Backed Mail Settings
        |--------------------------------------------------------------------------
        */

        try {
            app(MailSettingsService::class)->apply();
        } catch (\Throwable $exception) {
            /*
             * Keep the application running if the mail settings
             * table has not been created or mail settings fail.
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
