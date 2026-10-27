@extends('maintenance-manager.layouts.app')

@section('title', 'Maintenance Handover')

@section('content')
<div class="mx-auto max-w-3xl space-y-6 pb-10">
    <div>
        <a href="{{ route('maintenance-manager.schedules.index', ['date' => $date]) }}"
           class="text-sm font-semibold text-sky-700 hover:underline">← Back to Daily Maintenance Planning</a>
        <h1 class="mt-3 text-2xl font-bold text-slate-900">Record Maintenance Handover</h1>
        <p class="mt-1 text-sm text-slate-500">Preserve actual work completed by the outgoing plumber and authorize another plumber to continue.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="font-semibold">Please correct the following:</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm font-bold text-slate-900">{{ $workOrder->complaint?->complaint_no }} · Work Order #{{ $workOrder->id }}</p>
        <p class="mt-1 text-sm text-slate-600">Visit #{{ $workOrder->visit_number }} · {{ $date }} · In Progress · Approved</p>
        <p class="mt-2 text-xs text-slate-500">Started {{ $workOrder->started_at?->timezone('Asia/Manila')->format('M d, Y h:i A') }}</p>
    </div>

    <form method="POST" action="{{ route('maintenance-manager.schedules.handover.update', $workOrder) }}"
          class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        @csrf
        @method('PATCH')

        <div>
            <label for="old_technician_id" class="mb-1 block text-sm font-semibold text-slate-700">Outgoing plumber</label>
            <select id="old_technician_id" name="old_technician_id" required class="w-full rounded-xl border-slate-300 text-sm">
                <option value="">Select outgoing plumber</option>
                @foreach ($workOrder->technicians as $technician)
                    @php $participation = $workOrder->participations->firstWhere('technician_id', $technician->id); @endphp
                    <option value="{{ $technician->id }}" @selected(old('old_technician_id') == $technician->id)>
                        {{ $technician->full_name }} {{ $participation?->status === 'Active' ? '— Active participation recorded' : '— No active participation' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="new_technician_id" class="mb-1 block text-sm font-semibold text-slate-700">Replacement plumber (on duty)</label>
            <select id="new_technician_id" name="new_technician_id" required class="w-full rounded-xl border-slate-300 text-sm">
                <option value="">Select replacement</option>
                @foreach ($eligible as $duty)
                    <option value="{{ $duty->technician_id }}" @selected(old('new_technician_id') == $duty->technician_id)>
                        {{ $duty->technician?->full_name ?? 'Technician #' . $duty->technician_id }}
                    </option>
                @endforeach
            </select>
            @if ($eligible->isEmpty())
                <p class="mt-2 text-xs text-amber-700">No eligible replacement is on duty. Update duty management first.</p>
            @endif
        </div>

        <div>
            <label for="reason" class="mb-1 block text-sm font-semibold text-slate-700">Reason for handover</label>
            <textarea id="reason" name="reason" rows="3" minlength="10" maxlength="2000" required
                      class="w-full rounded-xl border-slate-300 text-sm" placeholder="Example: Original plumber became ill during field maintenance.">{{ old('reason') }}</textarea>
        </div>

        <div>
            <label for="contribution_notes" class="mb-1 block text-sm font-semibold text-slate-700">Actual work performed by outgoing plumber</label>
            <textarea id="contribution_notes" name="contribution_notes" rows="4" minlength="10" maxlength="3000" required
                      class="w-full rounded-xl border-slate-300 text-sm" placeholder="Describe the work that was actually performed before the handover.">{{ old('contribution_notes') }}</textarea>
            <p class="mt-1 text-xs text-slate-500">This is an auditable contribution record, not a completed visit.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" @disabled($eligible->isEmpty())
                    class="rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white hover:bg-sky-800 disabled:opacity-40">
                Confirm Handover
            </button>
            <a href="{{ route('maintenance-manager.schedules.index', ['date' => $date]) }}"
               class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700">Cancel</a>
        </div>
    </form>
</div>
@endsection
