<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\TechnicianDuty;
use App\Models\User;
use App\Services\TechnicianDutySchedulingService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TechnicianDutyController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate(['date' => ['nullable', 'date_format:Y-m-d']]);
        $date = $validated['date'] ?? CarbonImmutable::now('Asia/Manila')->toDateString();
        $plumbers = User::role('Maintenance Technician')->where('is_active', true)
            ->with('serviceArea')->orderBy('last_name')->orderBy('first_name')->get();
        $duties = TechnicianDuty::with('technician.serviceArea')->whereDate('duty_date', $date)
            ->orderBy('technician_id')->get();
        $scheduledCount = $duties->whereIn('status', ['Scheduled', 'On Duty'])->pluck('technician_id')->unique()->count();
        $unavailableCount = $duties->where('status', 'Off Duty')->count();
        return view('maintenance-manager.technician-duties.index', compact(
            'date', 'plumbers', 'duties', 'scheduledCount', 'unavailableCount'
        ));
    }

    public function generate(Request $request, TechnicianDutySchedulingService $service)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date_format:Y-m-d'],
            'days' => ['required', 'integer', 'between:1,31'],
        ]);
        $count = $service->generate($validated['start_date'], (int) $validated['days']);
        return redirect()->route('maintenance-manager.technician-duties.index', ['date' => $validated['start_date']])
            ->with('success', "Generated {$count} missing availability record(s). Existing adjustments were preserved.");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'technician_id' => ['required', 'integer', 'exists:users,id'],
            'duty_date' => ['required', 'date_format:Y-m-d'],
            'assigned_area' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Scheduled', 'Off Duty'])],
        ]);
        $plumber = User::role('Maintenance Technician')->where('is_active', true)
            ->findOrFail($data['technician_id']);
        if (TechnicianDuty::where('technician_id', $plumber->id)->whereDate('duty_date', $data['duty_date'])->exists()) {
            throw ValidationException::withMessages([
                'technician_id' => 'This plumber already has an availability record for this date. Use Adjust Availability instead.',
            ]);
        }
        TechnicianDuty::create([
            ...$data,
            'assigned_area' => $data['assigned_area'] ?: $plumber->serviceArea?->name,
            'start_time' => '08:00',
            'end_time' => '17:00',
            'duty_type' => 'Maintenance',
        ]);
        return redirect()->route('maintenance-manager.technician-duties.index', ['date' => $data['duty_date']])
            ->with('success', 'Availability record created.');
    }

    public function update(Request $request, TechnicianDuty $duty)
    {
        $data = $request->validate([
            'assigned_area' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Scheduled', 'Off Duty'])],
        ]);
        // Preserve existing shift hours and duty type for compatibility with scheduling validation.
        $duty->update($data);
        return redirect()->route('maintenance-manager.technician-duties.index', [
            'date' => $duty->duty_date->format('Y-m-d'),
        ])->with('success', 'Availability updated. Review existing work orders if this plumber is now unavailable.');
    }
}
