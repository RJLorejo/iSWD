
@php
    $suggestedDate = \Carbon\CarbonImmutable::now('Asia/Manila')
        ->startOfDay()
        ->addDay();

    while ($suggestedDate->isWeekend()) {
        $suggestedDate = $suggestedDate->addDay();
    }

    $originalTeamIds = $order->technicians
        ->pluck('id')
        ->map(fn ($id) => (int) $id)
        ->all();

    $followUpExists = \App\Models\MaintenanceWorkOrder::query()
        ->where('complaint_id', $order->complaint_id)
        ->where('id', '>', $order->id)
        ->whereNotIn('status', ['Cancelled', 'Rescheduled'])
        ->exists();

    $plumbers = \App\Models\User::role('Maintenance Technician')
        ->where('is_active', true)
        ->orderBy('first_name')
        ->get();
@endphp

@if ($followUpExists)
    <div class="rounded-xl border border-sky-200 bg-sky-50 p-3 text-sm text-sky-800">
        <i class="fas fa-calendar-check mr-1"></i>
        Follow-up already planned. Review its schedule before making changes.
    </div>
@else
    <details class="rounded-xl border border-slate-200 bg-white">
        <summary class="cursor-pointer list-none px-4 py-3 text-sm font-semibold text-sky-700">
            <i class="fas fa-calendar-plus mr-2"></i>
            Plan Follow-up Visit
            <i class="fas fa-chevron-down ml-2 text-xs"></i>
        </summary>

        <form method="POST"
            action="{{ route('maintenance-manager.schedules.pending-follow-up', $order) }}"
            class="space-y-4 border-t border-slate-100 p-4">

            @csrf

            <p class="text-xs leading-5 text-slate-500">
                Select a working date and maintenance team.
                Individual start and end times are not required.
                This creates a Draft schedule for manager finalization.
            </p>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">
                    Follow-up Date *
                </label>

                <input type="date"
                    name="schedule_date"
                    value="{{ old('schedule_date', $suggestedDate->format('Y-m-d')) }}"
                    min="{{ now('Asia/Manila')->toDateString() }}"
                    required
                    class="w-full rounded-xl border-slate-300 text-sm">

                <p class="mt-1 text-xs text-slate-500">
                    Suggested: {{ $suggestedDate->format('M d, Y') }}.
                    Choose a valid working day with available plumbers.
                </p>
            </div>

            <fieldset>
                <legend class="mb-2 text-xs font-semibold text-slate-700">
                    Assign Maintenance Team *
                </legend>

                <div class="max-h-44 space-y-1 overflow-y-auto rounded-xl border border-slate-200 p-2">
                    @forelse ($plumbers as $plumber)
                        <label class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-2 hover:bg-slate-50">
                            <input type="checkbox"
                                name="technician_ids[]"
                                value="{{ $plumber->id }}"
                                @checked(in_array(
                                    (int) $plumber->id,
                                    old('technician_ids', $originalTeamIds)
                                ))
                                class="rounded border-slate-300 text-sky-700">

                            <span class="text-xs text-slate-700">
                                {{ $plumber->full_name }}
                            </span>
                        </label>
                    @empty
                        <p class="p-2 text-xs text-slate-500">
                            No active maintenance technicians available.
                        </p>
                    @endforelse
                </div>

                <p class="mt-1 text-xs text-slate-500">
                    Technician duty for the selected date will be
                    verified when you save the follow-up.
                </p>
            </fieldset>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">
                    Manager Remarks
                </label>

                <textarea name="manager_remarks"
                    rows="2"
                    maxlength="2000"
                    placeholder="Optional follow-up instructions..."
                    class="w-full rounded-xl border-slate-300 text-sm">{{ old('manager_remarks') }}</textarea>
            </div>

            <button type="submit"
                onclick="return confirm('Create this follow-up work order in Draft?')"
                class="w-full rounded-xl bg-sky-700 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-800">
                <i class="fas fa-calendar-plus mr-1"></i>
                Save Draft Follow-up
            </button>
        </form>
    </details>
@endif
