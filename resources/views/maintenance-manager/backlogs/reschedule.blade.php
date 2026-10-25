@extends('maintenance-manager.layouts.app')

@section('title', 'Review Missed Visit')

@section('content')
<div class="mx-auto max-w-3xl space-y-5 pb-12">
    <div>
        <a href="{{ route('maintenance-manager.backlogs.index') }}" class="text-sm font-semibold text-sky-700 hover:underline">← Back to Missed Schedules</a>
        <h1 class="mt-3 text-2xl font-bold text-slate-900">Review & Reschedule Missed Visit</h1>
        <p class="mt-1 text-sm text-slate-500">Confirm that no work took place before authorizing a new Draft visit.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="font-bold">Please correct the following:</p>
            <ul class="mt-2 list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="font-bold text-slate-900">{{ $workOrder->complaint?->complaint_no }} · Work Order #{{ $workOrder->id }}</p>
        <p class="mt-1 text-sm text-slate-600">Visit #{{ $workOrder->visit_number }} · {{ $workOrder->schedule->schedule_date->format('M d, Y') }} · Approved</p>
        <p class="mt-2 text-sm text-slate-600">{{ $workOrder->complaint?->category?->name ?? 'Maintenance Concern' }}</p>
        <p class="mt-2 text-xs text-slate-500">Original team: {{ $workOrder->technicians->pluck('full_name')->join(', ') ?: 'Not recorded' }}</p>
    </div>

    <form method="POST" action="{{ route('maintenance-manager.backlogs.reschedule.store', $workOrder) }}"
          onsubmit="return confirm('Confirm this visit did NOT take place and create a replacement Draft visit? The original approved work order will be marked Rescheduled.');"
          class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        @csrf

        <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
            <p class="text-sm font-bold text-sky-900">Plumber's reported explanation</p>
            @if ($workOrder->missed_reported_at && filled($workOrder->missed_reason))
                <p class="mt-2 whitespace-pre-line text-sm text-sky-900">{{ $workOrder->missed_reason }}</p>
                <p class="mt-2 text-xs text-sky-700">Reported {{ $workOrder->missed_reported_at->timezone('Asia/Manila')->format('M d, Y h:i A') }}</p>
            @else
                <p class="mt-2 text-sm text-sky-800">No plumber report submitted. The manager may still verify and reschedule.</p>
            @endif
        </div>

        <div>
            <label for="missed_manager_reason" class="mb-1 block text-sm font-semibold text-slate-700">Manager's verified reason *</label>
            <textarea id="missed_manager_reason" name="missed_manager_reason" required minlength="10" maxlength="2000" rows="4"
                class="w-full rounded-xl border-slate-300 text-sm"
                placeholder="Explain how you confirmed the original visit did not take place, and why a replacement is necessary.">{{ old('missed_manager_reason') }}</textarea>
            <p class="mt-1 text-xs text-slate-500">Required whether or not the plumber submitted an explanation. The plumber's original report will not be overwritten.</p>
        </div>

        <div>
            <label for="schedule_date" class="mb-1 block text-sm font-semibold text-slate-700">Replacement working date *</label>
            <input id="schedule_date" type="date" name="schedule_date" required
                min="{{ now('Asia/Manila')->toDateString() }}"
                value="{{ old('schedule_date', $suggestedDate->toDateString()) }}"
                class="w-full rounded-xl border-slate-300 text-sm">
            <p class="mt-1 text-xs text-slate-500">Weekends and unavailable plumbers are rejected when submitted.</p>
        </div>

        <fieldset>
            <legend class="mb-2 text-sm font-semibold text-slate-700">Replacement maintenance team *</legend>
            @php
                $previousIds = $workOrder->technicians->pluck('id')->map(fn ($id) => (int) $id)->all();
                $checkedIds = array_map('intval', old('technician_ids', $previousIds));
            @endphp
            <div class="max-h-56 space-y-1 overflow-y-auto rounded-xl border border-slate-200 p-2">
                @forelse ($plumbers as $plumber)
                    <label class="flex items-center gap-2 rounded-lg p-2 hover:bg-slate-50">
                        <input type="checkbox" name="technician_ids[]" value="{{ $plumber->id }}"
                            @checked(in_array((int) $plumber->id, $checkedIds, true)) class="rounded border-slate-300 text-sky-700">
                        <span class="text-sm text-slate-700">{{ $plumber->full_name }}</span>
                    </label>
                @empty
                    <p class="p-3 text-sm text-amber-700">No active plumbers found. Update plumber availability first.</p>
                @endforelse
            </div>
            <p class="mt-1 text-xs text-slate-500">Availability is validated for the replacement date. The previous team is preselected but can be changed.</p>
        </fieldset>

        <div>
            <label for="manager_remarks" class="mb-1 block text-sm font-semibold text-slate-700">Manager instructions (optional)</label>
            <textarea id="manager_remarks" name="manager_remarks" rows="3" maxlength="2000"
                class="w-full rounded-xl border-slate-300 text-sm">{{ old('manager_remarks') }}</textarea>
        </div>

        <label class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            <input type="checkbox" name="confirm_no_work" value="1" required class="mt-1 rounded border-amber-300">
            <span><strong>I confirm no maintenance work was started or performed during the original visit.</strong>
            If any work took place, do not use this workflow; review the actual visit and participation records.</span>
        </label>

        <div class="flex flex-wrap gap-3">
            <button type="submit" @disabled($plumbers->isEmpty())
                class="rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white hover:bg-sky-800 disabled:opacity-40">
                Confirm & Create Draft Visit
            </button>
            <a href="{{ route('maintenance-manager.backlogs.index') }}"
                class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700">Cancel</a>
        </div>
    </form>
</div>
@endsection
