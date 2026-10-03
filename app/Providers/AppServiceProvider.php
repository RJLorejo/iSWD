<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Complaint;
use App\Observers\ComplaintObserver;
use App\Models\MaintenanceReport;
use App\Observers\MaintenanceReportObserver;
use App\Models\ServiceAnnouncement;
use Illuminate\Support\Facades\View;


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
