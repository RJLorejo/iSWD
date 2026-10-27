<?php

namespace App\Services;

use App\Models\TechnicianDuty;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TechnicianDutySchedulingService
{
    public const TIMEZONE = 'Asia/Manila';

    /**
     * Generate technician duty records for working days.
     *
     * Existing records are preserved, including:
     * - On Duty
     * - Scheduled
     * - On Leave
     * - Unavailable
     *
     * A technician may handle multiple maintenance
     * complaints during the same working day.
     */
    public function generate(
        string $startDate,
        int $days = 14
    ): int {
        if ($days < 1 || $days > 31) {
            throw ValidationException::withMessages([
                'days' => 'Choose between 1 and 31 calendar days.',
            ]);
        }

        try {
            $start = CarbonImmutable::createFromFormat(
                '!Y-m-d',
                $startDate,
                self::TIMEZONE
            );
        } catch (\Throwable $exception) {
            throw ValidationException::withMessages([
                'start_date' => 'Invalid start date.',
            ]);
        }

        if (
            !$start ||
            $start->format('Y-m-d') !== $startDate
        ) {
            throw ValidationException::withMessages([
                'start_date' => 'Invalid start date.',
            ]);
        }

        $today = CarbonImmutable::now(
            self::TIMEZONE
        )->startOfDay();

        if ($start->lt($today)) {
            throw ValidationException::withMessages([
                'start_date' => 'Cannot generate duties in the past.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Regular Duty Hours
        |--------------------------------------------------------------------------
        */

        $startTime = config(
            'maintenance.regular_duty_start',
            '08:00'
        );

        $endTime = config(
            'maintenance.regular_duty_end',
            '17:00'
        );

        if (
            !is_string($startTime) ||
            !is_string($endTime) ||
            !preg_match(
                '/^([01]\d|2[0-3]):[0-5]\d$/',
                $startTime
            ) ||
            !preg_match(
                '/^([01]\d|2[0-3]):[0-5]\d$/',
                $endTime
            ) ||
            $endTime <= $startTime
        ) {
            throw ValidationException::withMessages([
                'hours' => 'Configure valid regular duty hours before generating schedules.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Active Maintenance Technicians
        |--------------------------------------------------------------------------
        */

        $plumbers = User::role(
            'Maintenance Technician'
        )
            ->where('is_active', true)
            ->with('serviceArea')
            ->orderBy('id')
            ->get();

        if ($plumbers->isEmpty()) {
            return 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Working-Day Duties
        |--------------------------------------------------------------------------
        */

        $created = 0;

        DB::transaction(function () use (
            $start,
            $days,
            $plumbers,
            $startTime,
            $endTime,
            &$created
        ) {
            foreach (range(0, $days - 1) as $offset) {
                $date = $start->addDays($offset);

                /*
                 * Do not generate regular duties
                 * for Saturday or Sunday.
                 */
                if ($date->isWeekend()) {
                    continue;
                }

                $dateString = $date->toDateString();

                foreach ($plumbers as $plumber) {

                    /*
                     * Preserve existing duty records.
                     *
                     * Do not reset leave, unavailable,
                     * or manually adjusted schedules.
                     */
                    $duty = TechnicianDuty::query()
                        ->firstOrCreate(
                            [
                                'technician_id' => $plumber->id,
                                'duty_date' => $dateString,
                            ],
                            [
                                'start_time' => $startTime,
                                'end_time' => $endTime,
                                'assigned_area' => $plumber
                                    ->serviceArea?->name,
                                'duty_type' => 'Maintenance',
                                'status' => 'Scheduled',
                            ]
                        );

                    if ($duty->wasRecentlyCreated) {
                        $created++;
                    }
                }
            }
        }, 3);

        return $created;
    }
}
