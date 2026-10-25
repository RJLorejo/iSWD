<?php

namespace App\Services;

use App\Models\TechnicianDuty;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class TechnicianAvailabilityService
{
    private const TIMEZONE = 'Asia/Manila';

    /**
     * Get plumbers who have an available duty
     * on the selected maintenance date.
     */
    public function availableOn(string $date): Collection
    {
        $this->validateDate($date);

        return TechnicianDuty::query()
            ->with(['technician.serviceArea'])
            ->whereDate('duty_date', $date)
            ->whereIn('status', [
                'Scheduled',
                'On Duty',
            ])
            ->whereHas('technician', function ($query) {
                $query->role('Maintenance Technician')
                    ->where('is_active', true);
            })
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Verify that all selected technicians are available.
     */
    public function validateTechnicians(
        array $technicianIds,
        string $date
    ): void {
        $technicianIds = array_values(array_unique(
            array_map('intval', $technicianIds)
        ));

        if (empty($technicianIds)) {
            throw ValidationException::withMessages([
                'technician_ids' =>
                    'Select at least one plumber.',
            ]);
        }

        $availableIds = $this->availableOn($date)
            ->pluck('technician_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $unavailableIds = array_diff(
            $technicianIds,
            $availableIds
        );

        if (!empty($unavailableIds)) {
            throw ValidationException::withMessages([
                'technician_ids' =>
                    'One or more selected plumbers are unavailable '
                    . 'on the selected maintenance date.',
            ]);
        }
    }

    /**
     * Validate the date format without changing it.
     */
    private function validateDate(string $date): void
    {
        try {
            $parsed = CarbonImmutable::createFromFormat(
                '!Y-m-d',
                $date,
                self::TIMEZONE
            );
        } catch (\Throwable $exception) {
            throw ValidationException::withMessages([
                'schedule_date' => 'Invalid maintenance date.',
            ]);
        }

        if (
            !$parsed ||
            $parsed->format('Y-m-d') !== $date
        ) {
            throw ValidationException::withMessages([
                'schedule_date' => 'Invalid maintenance date.',
            ]);
        }
    }
}
