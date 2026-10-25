@php
    $maintenanceScheduling = app(\App\Services\MaintenanceSchedulingService::class);

    $planningDate = \Carbon\CarbonImmutable::parse($date, 'Asia/Manila')->toDateString();

    $planningData = collect($planningComplaints)
        ->map(function ($complaint) use ($maintenanceScheduling, $planningDate) {
            $aiAnalysis = $complaint->aiAnalysis;

            $finalUrgency = $aiAnalysis?->verified_urgency_level ?: $aiAnalysis?->urgency_level ?: 'Not Assessed';

            return [
                'id' => (int) $complaint->id,
                'number' => $complaint->complaint_no,
                'description' => $complaint->description,
                'category' => $complaint->category?->name ?? 'Uncategorized',
                'urgency' => $finalUrgency,
                'address' => $complaint->address,
                'earliest_date' => $maintenanceScheduling->earliestEligibleDate($complaint)->toDateString(),
                'show_url' => route('maintenance-manager.complaints.show', [
                    'complaint' => $complaint->id,
                    'source' => 'schedule',
                    'date' => $planningDate,
                    'section' => 'ready',
                ]),
            ];
        })
        ->values()
        ->all();

    $plumberData = collect($availableDuties)
        ->map(function ($duty) use ($plumberWorkloads) {
            return [
                'id' => (int) $duty->technician_id,
                'name' => $duty->technician?->full_name ?? 'Plumber #' . $duty->technician_id,
                'area' => $duty->assigned_area,
                'workload' => (int) ($plumberWorkloads[$duty->technician_id]['count'] ?? 0),
            ];
        })
        ->values()
        ->all();

    $oldJobs = collect(old('jobs', []))
        ->map(function ($job) {
            return [
                'complaint_id' => (int) ($job['complaint_id'] ?? 0),
                'technician_ids' => array_values(array_unique(array_map('intval', $job['technician_ids'] ?? []))),
                'override_reason' => (string) ($job['override_reason'] ?? ''),
            ];
        })
        ->filter(fn($job) => $job['complaint_id'] > 0)
        ->values()
        ->all();

    $preselectedComplaintId = (int) request()->query('complaint_id', 0);
@endphp

<div x-data="dailyWorkloadPlanner({
    date: @js($planningDate),
    complaints: @js($planningData),
    plumbers: @js($plumberData),
    oldJobs: @js($oldJobs),
    preselectedComplaintId: @js($preselectedComplaintId)
})" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    {{-- HEADER --}}
    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 p-5 sm:p-6">
        <div>
            <h2 class="text-lg font-bold text-slate-900">
                <i class="fas fa-clipboard-list mr-2 text-sky-700"></i>
                Daily Maintenance Workload Planning
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Select complaints and assign on-duty plumbers for the daily workload.
            </p>
        </div>


        @if ($isFinalized)
            <span
                class="inline-flex items-center gap-2 rounded-full
               bg-amber-50 px-3 py-1.5 text-xs
               font-semibold text-amber-700">
                <i class="fas fa-plus-circle"></i>
                Additional Workload
            </span>
        @else
            <span
                class="inline-flex items-center gap-2 rounded-full
               bg-sky-50 px-3 py-1.5 text-xs
               font-semibold text-sky-700">
                <i class="fas fa-pen-to-square"></i>
                Draft Planning
            </span>
        @endif

    </div>

    {{-- SELECTED DATE --}}
    <div class="mx-5 mt-5 rounded-xl border border-sky-200 bg-sky-50 p-4 sm:mx-6">
        <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">
            Selected Working Day
        </p>

        <p class="mt-1 text-base font-bold text-sky-900">
            {{ \Carbon\CarbonImmutable::parse($planningDate)->format('F d, Y (l)') }}
        </p>
    </div>


    {{-- FRIENDLY SERVER ERRORS --}}
    @if ($errors->any())
        <div class="mx-5 mt-5 rounded-xl border border-red-200 bg-red-50 p-4 sm:mx-6" role="alert"
            aria-live="assertive">
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700">
                    <i class="fas fa-circle-exclamation"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-bold text-red-900">
                        We couldn't save the daily workload
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-red-800">
                        Please review the following items, correct them,
                        and try saving again.
                    </p>

                    <ul class="mt-3 space-y-2">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-start gap-2 text-sm text-red-800">
                                <i class="fas fa-circle-xmark mt-0.5 shrink-0"></i>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <p class="mt-3 text-xs text-red-700">
                        Your selections may still be available on this page.
                        Review the assignments before submitting again.
                    </p>
                </div>
            </div>
        </div>
    @endif


    {{-- COMPLAINTS --}}
    <section class="p-5 sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-900">
                    <i class="fas fa-list-check mr-2 text-sky-700"></i>
                    Complaints for Planning
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Select a complaint to assign its maintenance team.
                </p>
            </div>

            <p class="text-xs font-semibold text-sky-700">
                <span x-text="jobs.length"></span> selected
            </p>
        </div>

        {{-- SEARCH + URGENCY --}}
        <div class="mt-5 grid gap-3 md:grid-cols-[minmax(0,1fr)_220px]">
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Search Complaints
                </label>

                <div class="relative">
                    <i class="fas fa-magnifying-glass absolute left-3 top-3.5 text-xs text-slate-400"></i>

                    <input type="search" x-model.debounce.250ms="complaintSearch" @input="complaintPage = 1"
                        placeholder="Complaint number, category, address..."
                        class="w-full rounded-xl border-slate-200 py-2.5 pl-9 pr-3 text-sm">
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Urgency
                </label>

                <select x-model="urgencyFilter" @change="complaintPage = 1"
                    class="w-full rounded-xl border-slate-200 text-sm">
                    <option value="">All Urgency Levels</option>

                    <template x-for="urgency in urgencies" :key="urgency">
                        <option :value="urgency" x-text="urgency"></option>
                    </template>
                </select>
            </div>
        </div>

        {{-- TOP ACTIONS --}}
        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
            <button type="button" @click="clearFilters()"
                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100">
                <i class="fas fa-rotate-left"></i>
                Clear Filters
            </button>

            <button type="button" @click="printComplaints()"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-print"></i>
                Print List
            </button>
        </div>

        {{-- COMPLAINT CARDS --}}
        <div class="mt-5 space-y-3">
            <template x-for="complaint in paginatedComplaints" :key="complaint.id">
                <div class="rounded-xl border p-4 transition"
                    :class="isSelected(complaint.id) ?
                        'border-sky-300 bg-sky-50/40' :
                        'border-slate-200 bg-white hover:border-slate-300'">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" :checked="isSelected(complaint.id)" @change="toggleComplaint(complaint)"
                            :aria-label="'Select ' + complaint.number"
                            class="mt-1 rounded border-slate-300 text-sky-600 focus:ring-sky-500">

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-bold text-slate-900" x-text="complaint.number"></span>

                                <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                    :class="urgencyClass(complaint.urgency)"
                                    x-text="complaint.urgency + ' Urgency'"></span>

                                <span x-show="isEarly(complaint)"
                                    class="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800">
                                    Early Date Review
                                </span>
                            </div>

                            <p class="mt-2 text-sm font-semibold text-slate-800" x-text="complaint.category"></p>

                            <p class="mt-1 text-xs text-slate-500" x-text="complaint.address || 'No address provided'">
                            </p>

                            <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500"
                                x-text="complaint.description || 'No description provided'"></p>

                            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                                <p class="text-xs text-slate-500">
                                    Recommended:
                                    <span class="font-semibold text-slate-700"
                                        x-text="formatDate(complaint.earliest_date)"></span>
                                </p>

                                <a :href="complaint.show_url" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 rounded-lg border border-sky-200 bg-white px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-50">
                                    <i class="fas fa-eye"></i>
                                    Show Details
                                </a>
                            </div>

                            {{-- ASSIGNED TEAM SUMMARY --}}
                            <div x-show="isSelected(complaint.id)" x-cloak
                                class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-sky-200 bg-white p-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-700">
                                        Assigned Team
                                    </p>

                                    <p class="mt-1 text-xs text-slate-600" x-text="teamSummary(complaint.id)"></p>
                                </div>

                                <button type="button" @click="openTeamModal(complaint.id)"
                                    class="inline-flex items-center gap-2 rounded-lg bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-100">
                                    <i class="fas fa-users-gear"></i>
                                    Edit Team
                                </button>
                            </div>

                            {{-- EARLY DATE REASON --}}
                            <template x-if="isSelected(complaint.id) && isEarly(complaint)">
                                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                    <label class="mb-2 block text-xs font-bold text-amber-900">
                                        Manager Reason for Earlier Scheduling
                                        <span class="text-red-600">*</span>
                                    </label>

                                    <textarea x-model="getJob(complaint.id).override_reason" rows="3" maxlength="2000"
                                        placeholder="Explain why this complaint must be scheduled earlier..."
                                        class="w-full rounded-xl border-amber-200 text-sm"></textarea>

                                    <p class="mt-1 text-xs text-amber-800">
                                        Minimum 10 characters. Recorded in scheduling history.
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="filteredComplaints.length === 0" x-cloak
                class="rounded-xl border border-dashed border-slate-200 px-5 py-10 text-center">
                <i class="fas fa-magnifying-glass text-2xl text-slate-300"></i>

                <p class="mt-3 text-sm font-semibold text-slate-700">
                    No matching complaints
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Try another search or urgency level.
                </p>
            </div>
        </div>

        {{-- PAGINATION + SAVE --}}
        <div class="mt-5 flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-4">
            <div class="flex flex-wrap items-center gap-3">
                <p class="text-xs text-slate-500">
                    <span x-text="firstRecord"></span>–<span x-text="lastRecord"></span>
                    of <span x-text="filteredComplaints.length"></span>
                </p>

                <button type="button" @click="complaintPage = Math.max(1, complaintPage - 1)"
                    :disabled="complaintPage <= 1"
                    class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 disabled:opacity-40">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <span class="text-xs font-semibold text-slate-600"
                    x-text="`${complaintPage} / ${totalComplaintPages}`"></span>

                <button type="button" @click="complaintPage = Math.min(totalComplaintPages, complaintPage + 1)"
                    :disabled="complaintPage >= totalComplaintPages"
                    class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 disabled:opacity-40">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <button type="submit" form="daily-workload-form"
                :disabled="jobs.length === 0 || plumbers.length === 0 || submitting"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-50">
                <i class="fas" :class="submitting ? 'fa-spinner fa-spin' : 'fa-floppy-disk'"></i>

                <span x-text="submitting ? 'Saving...' : 'Save Daily Workload'"></span>
            </button>
        </div>

        @if ($isFinalized)
            <div class="mt-4 rounded-xl border border-amber-200
                bg-amber-50 p-4">
                <p class="text-sm font-semibold text-amber-900">
                    <i class="fas fa-circle-info mr-1"></i>
                    Adding to a Finalized Schedule
                </p>

                <p class="mt-2 text-xs leading-6 text-amber-800">
                    New complaints will be saved as Draft Additions.
                    Previously approved work orders remain unchanged.
                    The new assignments will appear in the Technician
                    Schedule only after manager approval.
                </p>
            </div>
        @else
            <p class="mt-3 text-xs text-slate-500">
                The daily workload will be saved as Draft.
                Finalize the schedule separately.
            </p>
        @endif

        {{-- CLIENT ERROR --}}
        <div x-show="error" x-cloak role="alert"
            class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
            <i class="fas fa-circle-exclamation mr-1"></i>
            <span x-text="error"></span>
        </div>


    </section>

    {{-- HIDDEN FORM --}}
    <form id="daily-workload-form" method="POST" action="{{ route('maintenance-manager.schedules.batch-store') }}"
        @submit="validateSubmission($event)">
        @csrf

        <input type="hidden" name="schedule_date" value="{{ $planningDate }}">

        <template x-for="(job, index) in jobs" :key="job.complaint_id">
            <div>
                <input type="hidden" :name="`jobs[${index}][complaint_id]`" :value="job.complaint_id">

                <input type="hidden" :name="`jobs[${index}][override_reason]`" :value="job.override_reason">

                <template x-for="(technicianId, technicianIndex) in job.technician_ids" :key="technicianId">
                    <input type="hidden" :name="`jobs[${index}][technician_ids][${technicianIndex}]`"
                        :value="technicianId">
                </template>
            </div>
        </template>
    </form>

    {{-- PRINT-ONLY LIST --}}
    <div id="maintenance-planning-print" class="hidden">
        <h1 class="text-xl font-bold">Sagay Water District</h1>
        <h2 class="mt-1 text-base font-semibold">
            Maintenance Planning — Complaint Queue
        </h2>

        <p class="mt-2 text-sm">
            Planning Date:
            {{ \Carbon\CarbonImmutable::parse($planningDate)->format('F d, Y') }}
        </p>

        <table class="mt-5 w-full border-collapse text-left text-xs">
            <thead>
                <tr>
                    <th>Complaint No.</th>
                    <th>Category</th>
                    <th>Urgency</th>
                    <th>Address</th>
                    <th>Recommended Date</th>
                </tr>
            </thead>

            <tbody>
                <template x-for="complaint in filteredComplaints" :key="complaint.id">
                    <tr>
                        <td x-text="complaint.number"></td>
                        <td x-text="complaint.category"></td>
                        <td x-text="complaint.urgency"></td>
                        <td x-text="complaint.address || 'Not provided'"></td>
                        <td x-text="formatDate(complaint.earliest_date)"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- ========================================================= --}}
    {{-- PLUMBER ASSIGNMENT MODAL --}}
    {{-- ========================================================= --}}
    <template x-teleport="body">
        <div x-show="teamModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-3 sm:p-6"
            @keydown.escape.window="cancelTeamModal()" role="presentation">
            {{-- BACKDROP --}}
            <div class="absolute inset-0 bg-slate-950/60" @click="cancelTeamModal()"></div>

            {{-- MODAL --}}
            <div role="dialog" aria-modal="true" aria-labelledby="team-modal-title"
                class="relative flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                {{-- MODAL HEADER --}}
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
                    <div>
                        <h3 id="team-modal-title" class="text-lg font-bold text-slate-900">
                            Assign Maintenance Team
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            <span x-text="activeComplaint?.number || ''"></span>
                            <span x-show="activeComplaint"> — </span>
                            <span x-text="activeComplaint?.category || ''"></span>
                        </p>
                    </div>

                    <button type="button" @click="cancelTeamModal()" aria-label="Close plumber selection"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                {{-- SEARCH --}}
                <div class="border-b border-slate-100 px-5 py-4">
                    <div class="relative">
                        <i class="fas fa-magnifying-glass absolute left-3 top-3.5 text-xs text-slate-400"></i>

                        <input x-ref="teamSearchInput" type="search" x-model="teamSearch"
                            placeholder="Search plumber name or area..."
                            class="w-full rounded-xl border-slate-200 py-2.5 pl-9 pr-3 text-sm">
                    </div>

                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                        <p class="text-xs text-slate-500">
                            <span x-text="filteredTeamPlumbers.length"></span>
                            plumber(s) found
                        </p>

                        <p class="text-xs font-semibold text-sky-700">
                            <span x-text="temporaryTechnicianIds.length"></span>
                            selected
                        </p>
                    </div>
                </div>

                {{-- SCROLLABLE PLUMBER LIST --}}
                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-3">
                    <div class="space-y-2">
                        <template x-for="plumber in filteredTeamPlumbers" :key="plumber.id">
                            <label
                                class="flex cursor-pointer items-center justify-between gap-3 rounded-xl border p-3 transition"
                                :class="temporaryTechnicianIds.includes(plumber.id) ?
                                    'border-sky-300 bg-sky-50' :
                                    'border-slate-200 hover:bg-slate-50'">
                                <span class="flex min-w-0 items-center gap-3">
                                    <input type="checkbox" :checked="temporaryTechnicianIds.includes(plumber.id)"
                                        @change="toggleTemporaryPlumber(plumber.id)"
                                        class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">

                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-semibold text-slate-800"
                                            x-text="plumber.name"></span>

                                        <span class="mt-0.5 block text-xs text-slate-500"
                                            x-text="plumber.area || 'No assigned area'"></span>
                                    </span>
                                </span>

                                <span
                                    class="shrink-0 rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600"
                                    x-text="plumber.workload + ' jobs'"></span>
                            </label>
                        </template>

                        <div x-show="filteredTeamPlumbers.length === 0"
                            class="rounded-xl border border-dashed border-slate-200 p-8 text-center">
                            <p class="text-sm font-semibold text-slate-700">
                                No plumbers found
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Try another name or area.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- MODAL FOOTER --}}
                <div
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4">
                    <p class="text-xs text-slate-500">
                        Select one or more on-duty plumbers.
                    </p>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="cancelTeamModal()"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                            Cancel
                        </button>

                        <button type="button" @click="confirmTeamModal()"
                            :disabled="temporaryTechnicianIds.length === 0"
                            class="inline-flex items-center gap-2 rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-40">
                            <i class="fas fa-check"></i>
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

@once
    <style>
        [x-cloak] {
            display: none !important;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 12mm;
            }

            body.print-maintenance-planning * {
                visibility: hidden !important;
            }

            body.print-maintenance-planning #maintenance-planning-print,
            body.print-maintenance-planning #maintenance-planning-print * {
                visibility: visible !important;
            }

            body.print-maintenance-planning #maintenance-planning-print {
                display: block !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                padding: 0 !important;
                background: white !important;
                color: black !important;
            }

            #maintenance-planning-print table {
                width: 100%;
                border-collapse: collapse;
            }

            #maintenance-planning-print th,
            #maintenance-planning-print td {
                border: 1px solid #999;
                padding: 7px;
                vertical-align: top;
                overflow-wrap: anywhere;
            }

            #maintenance-planning-print th {
                background: #f1f5f9 !important;
                font-weight: bold;
            }

            #maintenance-planning-print tr {
                break-inside: avoid;
            }
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dailyWorkloadPlanner', (config) => ({
                planningDate: config.date,
                complaints: config.complaints || [],
                plumbers: config.plumbers || [],

                jobs: (config.oldJobs || [])
                    .map(job => ({
                        complaint_id: Number(job.complaint_id),
                        technician_ids: (job.technician_ids || [])
                            .map(Number),
                        override_reason: job.override_reason || ''
                    }))
                    .filter(job =>
                        (config.complaints || [])
                        .some(
                            complaint => complaint.id === job.complaint_id
                        )
                    ),

                complaintSearch: '',
                urgencyFilter: '',
                complaintPage: 1,
                perPage: 10,

                teamModalOpen: false,
                activeComplaintId: null,
                temporaryTechnicianIds: [],
                teamSearch: '',
                modalIsNewSelection: false,
                previousBodyOverflow: '',

                error: '',
                submitting: false,

                init() {
                    const complaintId = Number(
                        config.preselectedComplaintId || 0
                    );

                    if (!complaintId) {
                        return;
                    }

                    const complaint = this.complaints.find(
                        item => item.id === complaintId
                    );

                    if (!complaint) {
                        return;
                    }

                    this.complaintSearch = complaint.number;
                    this.complaintPage = 1;

                    if (!this.isSelected(complaintId)) {
                        this.toggleComplaint(complaint);
                    }
                },

                get urgencies() {
                    return [...new Set(
                        this.complaints
                        .map(item => item.urgency)
                        .filter(Boolean)
                    )].sort();
                },

                get filteredComplaints() {
                    const search = this.complaintSearch
                        .toLowerCase()
                        .trim();

                    return this.complaints.filter(complaint => {
                        const searchable = [
                            complaint.number,
                            complaint.category,
                            complaint.address,
                            complaint.description
                        ].join(' ').toLowerCase();

                        return (
                            (!search || searchable.includes(search)) &&
                            (!this.urgencyFilter ||
                                complaint.urgency === this.urgencyFilter)
                        );
                    });
                },

                get totalComplaintPages() {
                    return Math.max(
                        1,
                        Math.ceil(this.filteredComplaints.length / this.perPage)
                    );
                },

                get paginatedComplaints() {
                    const start = (this.complaintPage - 1) * this.perPage;

                    return this.filteredComplaints.slice(
                        start,
                        start + this.perPage
                    );
                },

                get firstRecord() {
                    if (this.filteredComplaints.length === 0) {
                        return 0;
                    }

                    return (this.complaintPage - 1) * this.perPage + 1;
                },

                get lastRecord() {
                    return Math.min(
                        this.complaintPage * this.perPage,
                        this.filteredComplaints.length
                    );
                },

                get filteredTeamPlumbers() {
                    const search = this.teamSearch.toLowerCase().trim();

                    return this.plumbers.filter(plumber =>
                        !search ||
                        String(plumber.name).toLowerCase().includes(search) ||
                        String(plumber.area || '').toLowerCase().includes(search)
                    );
                },

                get activeComplaint() {
                    return this.complaints.find(
                        complaint => complaint.id === this.activeComplaintId
                    ) || null;
                },

                clearFilters() {
                    this.complaintSearch = '';
                    this.urgencyFilter = '';
                    this.complaintPage = 1;
                },

                urgencyClass(urgency) {
                    const value = String(urgency || '').toLowerCase();

                    if (value === 'critical' || value === 'high') {
                        return 'bg-red-50 text-red-700';
                    }

                    if (value === 'medium' || value === 'moderate') {
                        return 'bg-amber-50 text-amber-700';
                    }

                    if (value === 'low') {
                        return 'bg-emerald-50 text-emerald-700';
                    }

                    return 'bg-slate-100 text-slate-600';
                },

                isEarly(complaint) {
                    return Boolean(
                        complaint.earliest_date &&
                        this.planningDate < complaint.earliest_date
                    );
                },

                formatDate(date) {
                    if (!date) {
                        return 'Not available';
                    }

                    const parts = String(date).split('-');

                    if (parts.length !== 3) {
                        return date;
                    }

                    const parsed = new Date(
                        Number(parts[0]),
                        Number(parts[1]) - 1,
                        Number(parts[2])
                    );

                    return parsed.toLocaleDateString('en-PH', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                },

                isSelected(id) {
                    return this.jobs.some(
                        job => job.complaint_id === id
                    );
                },

                getJob(id) {
                    return this.jobs.find(
                        job => job.complaint_id === id
                    );
                },

                teamSummary(complaintId) {
                    const job = this.getJob(complaintId);

                    if (!job || job.technician_ids.length === 0) {
                        return 'No plumbers assigned';
                    }

                    return job.technician_ids
                        .map(id => this.plumberName(id))
                        .join(', ');
                },

                plumberName(plumberId) {
                    const plumber = this.plumbers.find(
                        item => item.id === plumberId
                    );

                    return plumber ?
                        plumber.name :
                        `Plumber #${plumberId}`;
                },

                toggleComplaint(complaint) {
                    this.error = '';

                    if (this.isSelected(complaint.id)) {
                        this.jobs = this.jobs.filter(
                            job => job.complaint_id !== complaint.id
                        );

                        return;
                    }

                    this.jobs.push({
                        complaint_id: complaint.id,
                        technician_ids: [],
                        override_reason: ''
                    });

                    this.openTeamModal(complaint.id, true);
                },

                openTeamModal(complaintId, isNew = false) {
                    const job = this.getJob(complaintId);

                    if (!job) {
                        return;
                    }

                    this.activeComplaintId = complaintId;
                    this.temporaryTechnicianIds = [
                        ...job.technician_ids
                    ];

                    this.teamSearch = '';
                    this.modalIsNewSelection = isNew;
                    this.teamModalOpen = true;

                    this.previousBodyOverflow =
                        document.body.style.overflow;

                    document.body.style.overflow = 'hidden';

                    this.$nextTick(() => {
                        this.$refs.teamSearchInput?.focus();
                    });
                },


                toggleTemporaryPlumber(plumberId) {
                    const id = Number(plumberId);

                    if (!Number.isInteger(id) || id <= 0) {
                        return;
                    }

                    const selected = new Set(
                        this.temporaryTechnicianIds.map(Number)
                    );

                    if (selected.has(id)) {
                        selected.delete(id);
                    } else {
                        selected.add(id);
                    }

                    this.temporaryTechnicianIds = [...selected];
                },


                confirmTeamModal() {
                    const selectedIds = [
                        ...new Set(this.temporaryTechnicianIds.map(Number))
                    ];

                    if (selectedIds.length === 0) {
                        this.error = 'Select at least one plumber before confirming the team.';
                        return;
                    }

                    const job = this.getJob(this.activeComplaintId);

                    if (job) {
                        job.technician_ids = selectedIds;
                    }

                    this.error = '';
                    this.closeTeamModal();
                },

                cancelTeamModal() {
                    if (!this.teamModalOpen) {
                        return;
                    }

                    if (this.modalIsNewSelection) {
                        this.jobs = this.jobs.filter(
                            job => job.complaint_id !== this.activeComplaintId
                        );
                    }

                    this.closeTeamModal();
                },

                closeTeamModal() {
                    this.teamModalOpen = false;
                    this.activeComplaintId = null;
                    this.temporaryTechnicianIds = [];
                    this.modalIsNewSelection = false;

                    document.body.style.overflow =
                        this.previousBodyOverflow;
                },

                printComplaints() {
                    if (this.filteredComplaints.length === 0) {
                        this.error = 'No matching complaints to print.';
                        return;
                    }

                    this.error = '';

                    const cleanup = () => {
                        document.body.classList.remove(
                            'print-maintenance-planning'
                        );

                        window.removeEventListener('afterprint', cleanup);
                    };

                    window.addEventListener('afterprint', cleanup);

                    document.body.classList.add(
                        'print-maintenance-planning'
                    );

                    this.$nextTick(() => window.print());
                },


                fail(message, event) {
                    this.error = message;
                    this.submitting = false;

                    if (event) {
                        event.preventDefault();
                    }

                    this.$nextTick(() => {
                        const alert = this.$root.querySelector('[role="alert"]');

                        alert?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    });
                },

                validateSubmission(event) {
                    this.error = '';

                    if (this.submitting) {
                        event.preventDefault();
                        return;
                    }

                    if (this.jobs.length === 0) {
                        return this.fail(
                            'Select at least one complaint.',
                            event
                        );
                    }

                    for (const job of this.jobs) {
                        const complaint = this.complaints.find(
                            item => item.id === job.complaint_id
                        );

                        if (!complaint) {
                            return this.fail(
                                'A selected complaint is no longer available.',
                                event
                            );
                        }

                        if (job.technician_ids.length === 0) {
                            return this.fail(
                                `Please assign at least one plumber to ${complaint.number} before saving.`,
                                event
                            );
                        }

                        for (const plumberId of job.technician_ids) {
                            if (!this.plumbers.some(
                                    plumber => plumber.id === plumberId
                                )) {
                                return this.fail(
                                    `A plumber assigned to ${complaint.number} is no longer available. Please reopen the team selector and review the assignment.`,
                                    event
                                );
                            }
                        }

                        if (
                            this.isEarly(complaint) &&
                            String(job.override_reason || '').trim().length < 10
                        ) {
                            return this.fail(
                                `Provide a manager reason of at least 10 characters for ${complaint.number}.`,
                                event
                            );
                        }
                    }

                    this.submitting = true;
                }
            }));
        });
    </script>
@endonce
