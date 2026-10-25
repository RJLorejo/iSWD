@extends('maintenance-manager.layouts.app')
@section('title', 'Plumber Availability')
@section('content')
@php
    $availabilityPrintDate = \Carbon\Carbon::parse($date)->format('l, F j, Y');
    $availabilityPrintRecords = $duties->map(function ($duty) {
        $available = in_array($duty->status, ['Scheduled', 'On Duty'], true);
        return [
            'plumber' => $duty->technician?->full_name ?? 'Unknown Plumber',
            'area' => $duty->assigned_area ?: ($duty->technician?->serviceArea?->name ?? 'Area not specified'),
            'status' => $available ? 'Available' : ($duty->status === 'Off Duty' ? 'Unavailable' : 'Previously Completed'),
        ];
    })->values()->all();
@endphp

@php
    $today = now('Asia/Manila')->toDateString();
    $previousDate = \Carbon\Carbon::parse($date)->subDay()->toDateString();
    $nextDate = \Carbon\Carbon::parse($date)->addDay()->toDateString();
@endphp
<div class="mx-auto max-w-7xl space-y-5 pb-10">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">Maintenance Operations</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Plumber Availability</h1>
            <p class="mt-1 text-sm text-slate-500">Check which plumbers are available before assigning complaint work orders.</p>
        </div>
        <a href="{{ route('maintenance-manager.schedules.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-sky-700 hover:bg-slate-50">Daily Maintenance Planning <i class="fas fa-arrow-right ml-1"></i></a>
    </div>
    <button type="button" onclick="printPlumberAvailabilityReport()" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50"><i class="fas fa-print mr-1"></i> Print Report</button>
    @if(session('success'))<div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"><strong>Please check:</strong><ul class="mt-2 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Active Plumbers</p><p class="mt-2 text-3xl font-bold">{{ $plumbers->count() }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Available</p><p class="mt-2 text-3xl font-bold text-emerald-700">{{ $scheduledCount }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Unavailable</p><p class="mt-2 text-3xl font-bold text-rose-700">{{ $unavailableCount }}</p></div>
    </div>
    <div class="grid gap-4 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-bold text-slate-900">Generate Regular Availability</h2>
            <p class="mt-1 text-xs text-slate-500">Create missing weekday records. Existing changes are preserved.</p>
            <form method="POST" action="{{ route('maintenance-manager.technician-duties.generate') }}" class="mt-4 grid gap-3 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
                @csrf
                <div><label for="start_date" class="mb-1 block text-xs font-semibold text-slate-600">Start Date</label><input id="start_date" type="date" name="start_date" min="{{ $today }}" value="{{ old('start_date', max($today,$date)) }}" required class="w-full rounded-xl border-slate-300 text-sm"></div>
                <div><label for="days" class="mb-1 block text-xs font-semibold text-slate-600">Calendar Days</label><select id="days" name="days" class="w-full rounded-xl border-slate-300 text-sm">@foreach([7,14,21,28] as $days)<option value="{{ $days }}" @selected(old('days',14)==$days)>{{ $days }} days</option>@endforeach</select></div>
                <button class="rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-800">Generate</button>
            </form>
        </section>
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-bold text-slate-900">View Availability</h2>
            <p class="mt-1 text-xs text-slate-500">Select the date used for maintenance planning.</p>
            <form method="GET" action="{{ route('maintenance-manager.technician-duties.index') }}" class="mt-4 flex gap-2"><input type="date" name="date" value="{{ $date }}" required class="min-w-0 flex-1 rounded-xl border-slate-300 text-sm"><button class="rounded-xl border border-sky-200 bg-sky-50 px-4 text-sm font-semibold text-sky-800">View</button></form>
            <div class="mt-3 flex gap-2 text-xs font-semibold"><a class="rounded-lg border px-3 py-2" href="{{ route('maintenance-manager.technician-duties.index',['date'=>$previousDate]) }}">Previous</a><a class="rounded-lg border px-3 py-2" href="{{ route('maintenance-manager.technician-duties.index',['date'=>$today]) }}">Today</a><a class="rounded-lg border px-3 py-2" href="{{ route('maintenance-manager.technician-duties.index',['date'=>$nextDate]) }}">Next</a></div>
        </section>
    </div>
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 p-5"><div><h2 class="text-lg font-bold text-slate-900">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</h2><p class="text-xs text-slate-500">{{ $duties->count() }} availability records</p></div><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ \Carbon\Carbon::parse($date)->isWeekend() ? 'Weekend' : 'Weekday' }}</span></div>
        <div class="grid gap-4 p-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($duties as $duty)
                @php $available = in_array($duty->status,['Scheduled','On Duty'],true); @endphp
                <article class="rounded-2xl border border-slate-200 p-4" x-data="{editing:false}">
                    <div class="flex items-start justify-between gap-2"><div class="min-w-0"><h3 class="font-bold text-slate-900">{{ $duty->technician?->full_name ?? 'Unknown Plumber' }}</h3><p class="mt-1 text-xs text-slate-500">{{ $duty->assigned_area ?: ($duty->technician?->serviceArea?->name ?? 'Area not specified') }}</p></div><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $available ? 'bg-emerald-100 text-emerald-800' : ($duty->status === 'Off Duty' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700') }}">{{ $available ? 'Available' : ($duty->status === 'Off Duty' ? 'Unavailable' : 'Previously Completed') }}</span></div>
                    <button type="button" @click="editing=!editing" :aria-expanded="editing.toString()" class="mt-4 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-sky-700 hover:bg-slate-50">Adjust Availability</button>
                    <form x-show="editing" x-cloak method="POST" action="{{ route('maintenance-manager.technician-duties.update',$duty) }}" class="mt-4 space-y-3 border-t pt-4">@csrf @method('PATCH')
                        <div><label class="mb-1 block text-xs font-semibold text-slate-600">Assigned Area</label><input name="assigned_area" maxlength="255" value="{{ old('assigned_area',$duty->assigned_area) }}" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Use designated service area"></div>
                        <div><label class="mb-1 block text-xs font-semibold text-slate-600">Availability</label><select name="status" class="w-full rounded-lg border-slate-300 text-sm"><option value="Scheduled" @selected($available)>Available</option><option value="Off Duty" @selected($duty->status === 'Off Duty')>Unavailable</option></select></div>
                        <p class="text-xs text-amber-700">If this plumber has existing work orders, review them in Daily Maintenance Planning after marking unavailable.</p>
                        <button class="w-full rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white">Save Availability</button>
                    </form>
                </article>
            @empty
                <div class="col-span-full py-10 text-center text-sm text-slate-500">No availability records for this date. Generate regular records or add special coverage below.</div>
            @endforelse
        </div>
    </section>
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" x-data="{open:false}">
        <div class="flex items-center justify-between gap-3"><div><h2 class="font-bold text-slate-900">Add Special Availability</h2><p class="mt-1 text-xs text-slate-500">For dates without an existing plumber record, including weekend coverage.</p></div><button type="button" @click="open=!open" class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-700">+ Add</button></div>
        <form x-show="open" x-cloak method="POST" action="{{ route('maintenance-manager.technician-duties.store') }}" class="mt-4 grid gap-3 border-t pt-4 sm:grid-cols-2 lg:grid-cols-4">@csrf
            <div><label class="mb-1 block text-xs font-semibold">Plumber</label><select name="technician_id" required class="w-full rounded-lg border-slate-300 text-sm"><option value="">Select plumber</option>@foreach($plumbers as $plumber)<option value="{{ $plumber->id }}" @selected(old('technician_id')==$plumber->id)>{{ $plumber->full_name }}</option>@endforeach</select></div>
            <div><label class="mb-1 block text-xs font-semibold">Date</label><input type="date" name="duty_date" value="{{ old('duty_date',$date) }}" required class="w-full rounded-lg border-slate-300 text-sm"></div>
            <div><label class="mb-1 block text-xs font-semibold">Assigned Area</label><input name="assigned_area" maxlength="255" value="{{ old('assigned_area') }}" placeholder="Use designated area" class="w-full rounded-lg border-slate-300 text-sm"></div>
            <div><label class="mb-1 block text-xs font-semibold">Availability</label><select name="status" class="w-full rounded-lg border-slate-300 text-sm"><option value="Scheduled">Available</option><option value="Off Duty">Unavailable</option></select></div>
            <div class="sm:col-span-2 lg:col-span-4"><button class="rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white">Save</button></div>
        </form>
    </section>
    <p class="text-xs leading-5 text-slate-500">Availability supports complaint scheduling only; it does not track attendance or approve leave. Existing work orders must be reviewed separately when availability changes.</p>
</div>

@include('maintenance-manager.partials.report-print-template')
<script>
function printPlumberAvailabilityReport() {
    const records = @js($availabilityPrintRecords);
    const esc = v => String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    const tableHtml = '<thead><tr><th>Plumber</th><th>Assigned Area</th><th>Availability</th></tr></thead><tbody>' +
        (records.map(r=>'<tr><td>'+esc(r.plumber)+'</td><td>'+esc(r.area)+'</td><td>'+esc(r.status)+'</td></tr>').join('') || '<tr><td colspan="3">No availability records for this date.</td></tr>') + '</tbody>';
    openMaintenanceReport({title:'Plumber Availability Report',subtitle:'Maintenance Operations',period:@js($availabilityPrintDate),tableHtml,summary:`${records.length} availability records · ${@js($scheduledCount)} available · ${@js($unavailableCount)} unavailable`});
}
</script>

@endsection
