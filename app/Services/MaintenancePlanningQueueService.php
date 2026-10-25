<?php

namespace App\Services;

use App\Models\Complaint;
use Illuminate\Database\Eloquent\Builder;

class MaintenancePlanningQueueService
{

    public function readyForPlanning(): Builder
    {
        return Complaint::query()
            ->with([
                'division',
                'category',
                'consumer',
                'aiAnalysis',
                'commercialResolution',
            ])

            ->where(function (Builder $query) {

                $query->where(function (Builder $engineering) {

                    $engineering
                        ->where('complaints.status', 'Verified')
                        ->whereNotNull('complaints.verified_at')
                        ->whereHas('division', function (Builder $division) {

                            $division->where(
                                'divisions.name',
                                'like',
                                '%engineering%'
                            );

                        });

                })->orWhere(function (Builder $commercial) {

                    $commercial
                        ->where('complaints.status', 'For Maintenance')
                        ->whereHas(
                            'commercialResolution',
                            function (Builder $resolution) {

                                $resolution->whereNotNull(
                                    'commercial_resolutions.forwarded_to_maintenance_at'
                                );

                            }
                        );

                });

            })

            ->whereDoesntHave('workOrders', function (Builder $workOrders) {

                $workOrders->whereIn(
                    'maintenance_work_orders.status',
                    [
                        'Draft',
                        'Scheduled',
                        'In Progress',
                        'Pending Workload',
                    ]
                );

            })

            ->orderBy('complaints.created_at', 'asc')
            ->orderBy('complaints.id', 'asc');
    }

    /**
     * For Assignment and Daily Maintenance Planning
     * use the same eligible complaint queue.
     *
     * Historical complaint_technicians records
     * must not prevent planning eligibility.
     */
    public function unassigned(): Builder
    {
        return $this->readyForPlanning();
    }

    /**
     * Total complaints currently eligible
     * for maintenance planning.
     */
    public function unassignedCount(): int
    {
        return $this->readyForPlanning()->count();
    }
}
