<?php

namespace App\Services;

use App\Models\MaintenanceWorkOrder;
use App\Models\TechnicianDuty;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MaintenanceWorkOrderValidationService
{
    private const TIMEZONE = 'Asia/Manila';

    public function validateWorkingDate(
        string $date,
        string $field = 'schedule_date'
    ): CarbonImmutable {
        try {
            $target = CarbonImmutable::createFromFormat(
                '!Y-m-d',
                $date,
                self::TIMEZONE
            );
        } catch (\Throwable $exception) {
            $this->fail($field, 'Invalid maintenance date.');
        }

        if (!$target || $target->format('Y-m-d') !== $date) {
            $this->fail($field, 'Invalid maintenance date.');
        }

        if (
            $target->isWeekend() ||
            $target->lt(CarbonImmutable::now(self::TIMEZONE)->startOfDay())
        ) {
            $this->fail(
                $field,
                'Select a current or future working day.'
            );
        }

        return $target;
    }

    public function normalizeTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (string) $value;

        if (!preg_match('/^\d{2}:\d{2}(?::\d{2})?$/', $value)) {
            return null;
        }

        $time = substr($value, 0, 5);

        try {
            $parsed = CarbonImmutable::createFromFormat(
                '!H:i',
                $time,
                self::TIMEZONE
            );

            return $parsed && $parsed->format('H:i') === $time
                ? $time
                : null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    // Kept for older code that still uses time-based scheduling.
    public function validateTimeRange(
        mixed $start,
        mixed $end,
        string $field = 'end_time'
    ): array {
        $start = $this->normalizeTime($start);
        $end = $this->normalizeTime($end);

        if (!$start || !$end || $start >= $end) {
            $this->fail(
                $field,
                'The planned end time must be later than the start time.'
            );
        }

        return [$start, $end];
    }

    public function teamIds(MaintenanceWorkOrder $workOrder): array
    {
        $ids = DB::table('maintenance_work_order_technicians')
            ->where('maintenance_work_order_id', $workOrder->id)
            ->pluck('technician_id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();

        return $ids;
    }


    public function lockDuties(
        string $date,
        array $technicianIds
    ): Collection {
        $ids = array_values(array_unique(
            array_map('intval', $technicianIds)
        ));

        sort($ids);

        if (empty($ids)) {
            return collect();
        }

        return TechnicianDuty::query()
            ->whereDate('duty_date', $date)
            ->whereIn('technician_id', $ids)
            ->orderBy('technician_id')
            ->lockForUpdate()
            ->get()
            ->keyBy('technician_id');
    }

    /**
     * New workload-based validation:
     * A plumber may handle multiple complaints on the same day.
     */
    public function validateTechnicianForDate(
        int $technicianId,
        string $date,
        string $field = 'technician_ids',
        ?Collection $lockedDuties = null
    ): void {
        $active = User::query()
            ->role('Maintenance Technician')
            ->where('is_active', true)
            ->whereKey($technicianId)
            ->exists();

        if (!$active) {
            $this->fail(
                $field,
                "Plumber #{$technicianId} is inactive or is not a Maintenance Technician."
            );
        }

        $duty = $lockedDuties
            ? $lockedDuties->get($technicianId)
            : TechnicianDuty::query()
            ->where('technician_id', $technicianId)
            ->whereDate('duty_date', $date)
            ->first();

        if (
            !$duty ||
            !in_array($duty->status, ['Scheduled', 'On Duty'], true)
        ) {
            $this->fail(
                $field,
                "Plumber #{$technicianId} is not available on {$date}. Assign an available plumber or update the duty record."
            );
        }
    }

    /**
     * Legacy compatibility for code still passing time intervals.
     * Time is no longer used to decide whether a plumber can
     * receive multiple complaints on one working day.
     */
    public function validateTechnicianDuty(
        int $technicianId,
        string $date,
        string $start,
        string $end,
        string $field = 'technician_ids',
        ?Collection $lockedDuties = null
    ): void {
        $this->validateTechnicianForDate(
            $technicianId,
            $date,
            $field,
            $lockedDuties
        );
    }

    /**
     * Workload scheduling permits multiple jobs per plumber/day.
     * Retained for callers using the old method signature.
     */
    public function validateNoConflict(
        int $technicianId,
        string $date,
        string $start,
        string $end,
        ?int $excludeWorkOrderId = null,
        string $field = 'technician_ids'
    ): void {
        $this->validateTechnicianForDate(
            $technicianId,
            $date,
            $field
        );
    }

    /**
     * Multiple assignments to one plumber are permitted.
     * This method is retained for backward compatibility.
     */
    public function validateInternalOverlaps(
        array $assignments,
        string $field = 'jobs'
    ): void {
        // No time-overlap restrictions in daily workload planning.
    }

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([
            $field => $message,
        ]);
    }
}
