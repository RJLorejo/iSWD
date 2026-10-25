@props(['complaint', 'audience' => 'staff'])
@php
    $isConsumer = $audience === 'consumer';
    $orders = \App\Models\MaintenanceWorkOrder::query()
        ->with(['schedule', 'technicians'])
        ->where('complaint_id', $complaint->id)
        ->orderBy('visit_number')->orderBy('id')->get();
    $pendingOrder = $orders->filter(fn ($order) => filled($order->pending_reason) && $order->pending_recorded_at)->last();
    $followUp = $pendingOrder ? $orders->first(fn ($order) =>
        ((int) $order->visit_number > (int) $pendingOrder->visit_number ||
        ((int) $order->visit_number === (int) $pendingOrder->visit_number && (int) $order->id > (int) $pendingOrder->id)) &&
        !in_array($order->status, ['Cancelled', 'Rescheduled'], true)
    ) : null;
    $resumedOriginal = $pendingOrder && in_array($pendingOrder->status, ['In Progress', 'Completed'], true);
    $followUpConfirmed = $followUp && $followUp->approval_status === 'Approved' && $followUp->schedule?->status === 'Finalized';
    $showNotice = $pendingOrder && !in_array($complaint->status, ['Completed', 'Closed'], true);
    $recordedDate = $pendingOrder?->pending_recorded_at
        ? \Carbon\Carbon::parse($pendingOrder->pending_recorded_at)->timezone('Asia/Manila')->format('M d, Y · g:i A') : null;
    $displayStatus = $followUpConfirmed ? 'Follow-up Confirmed' : ($followUp ? 'Follow-up Under Review' : ($resumedOriginal ? 'Maintenance Resumed' : 'Pending Workload'));
@endphp
@if ($showNotice)
    <section class="overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-amber-100 bg-amber-50 px-5 py-4">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700"><i class="fas fa-clock"></i></div>
                <div>
                    <h3 class="font-semibold text-slate-900">{{ $isConsumer ? 'Maintenance Update' : 'Pending Workload and Follow-up' }}</h3>
                    <p class="mt-1 text-xs text-slate-600">{{ $isConsumer ? 'Latest maintenance information for your concern' : 'Work-order status and follow-up history' }}</p>
                </div>
            </div>
            <span class="rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-800">{{ $displayStatus }}</span>
        </div>
        <div class="space-y-4 p-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $isConsumer ? 'Reason maintenance was interrupted' : 'Recorded Pending Workload reason' }}</p>
                <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-slate-800">{{ $pendingOrder->pending_reason }}</p>
            </div>
            @unless ($isConsumer)
                <div class="grid gap-3 text-sm sm:grid-cols-2">
                    <p class="rounded-xl bg-slate-50 p-3">Original Work Order #{{ $pendingOrder->id }} · Visit {{ $pendingOrder->visit_number }}</p>
                    <p class="rounded-xl bg-slate-50 p-3">Reported: {{ $recordedDate ?: 'Not recorded' }}</p>
                </div>
            @endunless
            @if ($followUpConfirmed)
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    <p class="font-semibold text-emerald-900">Follow-up maintenance confirmed</p>
                    <p class="mt-1 text-sm text-emerald-800">{{ \Carbon\Carbon::parse($followUp->schedule->schedule_date)->format('F d, Y') }}</p>
                    @unless ($isConsumer)
                        <p class="mt-2 text-xs text-emerald-800">Work Order #{{ $followUp->id }} · Visit {{ $followUp->visit_number }} · Team: {{ $followUp->technicians->pluck('full_name')->filter()->implode(', ') ?: 'Not assigned' }}</p>
                    @endunless
                </div>
            @elseif ($followUp)
                <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                    <p class="font-semibold text-sky-900">Follow-up awaiting manager approval</p>
                    <p class="mt-1 text-sm text-sky-800">A new work order has been prepared. Its date is not confirmed until the manager approves it.</p>
                </div>
            @elseif ($resumedOriginal)
                <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                    <p class="font-semibold text-sky-900">Maintenance resumed on the original visit</p>
                    <p class="mt-1 text-sm text-sky-800">The assigned technician returned to continue the same maintenance visit.</p>
                </div>
            @else
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <p class="font-semibold text-amber-900">Maintenance follow-up pending</p>
                    <p class="mt-1 text-sm text-amber-800">{{ $isConsumer ? 'The maintenance team may return on the approved work date. If another visit is needed, a new date will be shown once confirmed.' : 'The original plumber may resume on the approved scheduled day. The manager can also create a follow-up work order.' }}</p>
                </div>
            @endif
        </div>
    </section>
@endif
