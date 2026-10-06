@extends('customer-service.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    @php
        $isCommercial = str_contains(strtolower((string) $complaint->division?->name), 'commercial');
        $isEngineering = str_contains(strtolower((string) $complaint->division?->name), 'engineering');

        $canEdit = $complaint->status === 'Pending' && (int) $complaint->customer_service_id === (int) auth()->id();

        $commercialResolution = $complaint->commercialResolution;
        $report = $complaint->maintenanceReport;
        $aiAnalysis = $complaint->aiAnalysis;

        $consumerCategory = $aiAnalysis?->consumerCategory ?? $complaint->category;
        $predictedCategory = $aiAnalysis?->predictedCategory;
        $verifiedCategory = $aiAnalysis?->verifiedCategory;

        $urgencyLevel = $aiAnalysis?->urgency_level;
        $urgencyLabel = $urgencyLevel ?: 'Not Assessed';

        $urgencyClasses = match ($urgencyLevel) {
            'High' => 'bg-red-50 border-red-200 text-red-700',
            'Moderate' => 'bg-amber-50 border-amber-200 text-amber-700',
            'Low' => 'bg-green-50 border-green-200 text-green-700',
            default => 'bg-gray-50 border-gray-200 text-gray-600',
        };

        $statusLabel = match ($complaint->status) {
            'CS Processing' => 'CS Processing',
            'For Maintenance' => 'For Maintenance',
            'Completed' => 'Accomplished',
            default => $complaint->status,
        };

        $statusClasses = match ($complaint->status) {
            'Pending' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Verified' => 'bg-green-50 text-green-700 border-green-200',
            'CS Processing' => 'bg-sky-50 text-sky-700 border-sky-200',
            'For Maintenance' => 'bg-violet-50 text-violet-700 border-violet-200',
            'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'In Progress' => 'bg-blue-50 text-blue-700 border-blue-200',
            'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Closed' => 'bg-slate-100 text-slate-700 border-slate-200',
            'Rejected' => 'bg-red-50 text-red-700 border-red-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };

        $displayAddress =
            $isCommercial && $complaint->consumer
                ? $complaint->consumer->address?->full_address ?? 'No registered account address recorded.'
                : ($complaint->address ?:
                'No address recorded.');

        $addressLabel =
            $isCommercial && $complaint->consumer
                ? 'Account Address'
                : ($isEngineering
                    ? 'Service Address'
                    : 'Address');

        $assignedNames = $complaint->technicians->pluck('full_name')->filter()->values();

        $firstAssignedAt = $complaint->technicians
            ->map(fn($technician) => $technician->pivot?->assigned_at)
            ->filter()
            ->sort()
            ->first();

        $firstStartedAt = $complaint->technicians
            ->map(fn($technician) => $technician->pivot?->started_at)
            ->filter()
            ->sort()
            ->first();

        $firstCompletedAt = $complaint->technicians
            ->map(fn($technician) => $technician->pivot?->completed_at)
            ->filter()
            ->sort()
            ->first();

        $hasMaintenanceStage =
            $complaint->status === 'For Maintenance' || $complaint->technicians->isNotEmpty() || $report;

        $isClosedWithoutMaintenance =
            $isCommercial && $complaint->status === 'Closed' && !$commercialResolution?->forwarded_to_maintenance_at;

        $rawAnalysis = is_array($aiAnalysis?->raw_analysis) ? $aiAnalysis->raw_analysis : [];

        $supportingSummary = data_get($rawAnalysis, 'supporting_evidence.summary');
        $supportingIndicators = data_get($rawAnalysis, 'supporting_evidence.indicators', []);
        $urgencyReasons = data_get($rawAnalysis, 'urgency.reasons', []);

        $verificationDivisions = $divisions
            ->map(function ($division) {
                return [
                    'id' => (int) $division->id,
                    'name' => $division->name,
                    'complaint_types' => $division->complaintTypes
                        ->map(function ($type) {
                            return [
                                'id' => (int) $type->id,
                                'name' => $type->name,
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    @endphp


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Header --}}
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

            <div class="min-w-0">

                <div class="flex flex-wrap items-center gap-2 mb-2">

                    <span class="text-sm font-medium text-gray-500">
                        Customer Service
                    </span>

                    <span class="text-gray-300">•</span>

                    <span class="text-sm font-semibold text-gray-700">
                        {{ $complaint->complaint_no }}
                    </span>

                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    {{ $complaint->category?->name ?? 'Water Service Concern' }}
                </h1>

                <p class="text-sm text-gray-500 mt-2">
                    Review the complaint, track its progress, and perform the available Customer Service action.
                </p>

            </div>

            <div class="flex flex-col sm:flex-row gap-2">

                <a href="{{ route('customer-service.complaints.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                    border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">

                    <i class="fas fa-arrow-left"></i>
                    Back

                </a>

                @if ($canEdit)
                    <a href="{{ route('customer-service.complaints.edit', $complaint) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                        bg-blue-600 text-sm font-semibold text-white hover:bg-blue-700">

                        <i class="fas fa-pen"></i>
                        Edit

                    </a>
                @endif

            </div>

        </div>


        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                <i class="fas fa-circle-check mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <i class="fas fa-circle-exclamation mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">

                <p class="font-semibold text-red-800">
                    Please correct the following:
                </p>

                <ul class="mt-2 list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif


        {{-- Overview --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">

            <div class="p-5 sm:p-6">

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Status
                        </p>

                        <span
                            class="mt-2 inline-flex items-center px-3 py-1.5 rounded-full border text-sm font-semibold {{ $statusClasses }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Urgency
                        </p>

                        <span
                            class="mt-2 inline-flex items-center px-3 py-1.5 rounded-full border text-sm font-semibold {{ $urgencyClasses }}">
                            {{ $urgencyLabel }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Division
                        </p>

                        <p class="mt-2 text-sm font-semibold text-gray-900">
                            {{ $complaint->division?->name ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Submitted
                        </p>

                        <p class="mt-2 text-sm font-semibold text-gray-900">
                            {{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}
                        </p>
                    </div>

                </div>

            </div>

        </div>


        {{-- Complaint Information --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">

            <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

                <h2 class="font-semibold text-gray-900">
                    Complaint Information
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Original concern and account information.
                </p>

            </div>

            <div class="p-5 sm:p-6 space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

                    <div>
                        <p class="text-xs text-gray-500">Complainant</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $complaint->complainant_name ?: '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Contact Number</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $complaint->complainant_phone ?: '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">SWD Account</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $complaint->consumer?->account_number ?: 'Walk-in / No linked account' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Complaint Type</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $complaint->category?->name ?? '—' }}
                        </p>
                    </div>

                </div>

                <div class="border-t border-gray-100 pt-5">

                    <p class="text-xs text-gray-500">
                        Your Concern
                    </p>

                    <div
                        class="mt-2 rounded-xl bg-gray-50 border border-gray-200 p-4 text-sm text-gray-700 whitespace-pre-line">
                        {{ $complaint->description }}
                    </div>

                </div>

                <div class="border-t border-gray-100 pt-5">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        <div>

                            <p class="text-xs text-gray-500">
                                {{ $addressLabel }}
                            </p>

                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $displayAddress }}
                            </p>

                            @if (!$isCommercial && $complaint->landmark)
                                <p class="mt-3 text-xs text-gray-500">
                                    Landmark
                                </p>

                                <p class="mt-1 text-sm text-gray-700">
                                    {{ $complaint->landmark }}
                                </p>
                            @endif

                        </div>

                        @if ($complaint->photo)
                            <div>

                                <p class="text-xs text-gray-500 mb-2">
                                    Submitted Photo
                                </p>

                                <img src="{{ asset('storage/' . $complaint->photo) }}" alt="Complaint photo"
                                    class="w-full max-h-72 object-cover rounded-xl border border-gray-200">

                            </div>
                        @endif

                    </div>

                    @if (!$isCommercial && $complaint->latitude && $complaint->longitude)
                        <div id="complaint-map" class="mt-5 w-full h-72 rounded-xl border border-gray-200 overflow-hidden">
                        </div>
                    @endif

                </div>

            </div>

        </div>


        {{-- Complaint Tracking --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">

            <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

                <h2 class="font-semibold text-gray-900">
                    Complaint Tracking
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Complete progress history for this complaint or service request.
                </p>

            </div>

            <div class="p-5 sm:p-6">

                <div class="max-w-4xl">

                    {{-- Submitted --}}
                    <div class="flex gap-4">

                        <div class="flex flex-col items-center">

                            <div
                                class="w-9 h-9 rounded-full bg-green-100 text-green-700 flex items-center justify-center shrink-0">
                                <i class="fas fa-check text-xs"></i>
                            </div>

                            <div class="w-px flex-1 min-h-12 bg-green-200"></div>

                        </div>

                        <div class="pb-6">

                            <p class="text-sm font-semibold text-gray-900">
                                {{ $isCommercial ? 'Request Submitted' : 'Complaint Submitted' }}
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                {{ $complaint->created_at?->format('M d, Y • h:i A') }}
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                Submitted by {{ $complaint->complainant_name ?: 'Consumer' }}
                            </p>

                        </div>

                    </div>


                    {{-- Verification --}}
                    @php
                        $verificationReached =
                            $complaint->verified_at || !in_array($complaint->status, ['Pending'], true);
                    @endphp

                    <div class="flex gap-4">

                        <div class="flex flex-col items-center">

                            <div
                                class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                {{ $verificationReached ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">

                                <i class="fas {{ $verificationReached ? 'fa-check' : 'fa-circle' }} text-xs"></i>

                            </div>

                            @if ($complaint->status !== 'Rejected')
                                <div
                                    class="w-px flex-1 min-h-12 {{ $verificationReached ? 'bg-green-200' : 'bg-gray-200' }}">
                                </div>
                            @endif

                        </div>

                        <div class="pb-6">

                            <p
                                class="text-sm font-semibold {{ $verificationReached ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $complaint->status === 'Rejected' ? 'Rejected' : 'Verified' }}
                            </p>

                            @if ($complaint->verified_at)
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $complaint->verified_at->format('M d, Y • h:i A') }}
                                </p>

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $complaint->status === 'Rejected' ? 'Reviewed' : 'Verified' }}
                                    by {{ $complaint->verifier?->full_name ?? 'Customer Service' }}
                                </p>
                            @else
                                <p class="text-xs text-gray-400 mt-1">
                                    Waiting for Customer Service verification
                                </p>
                            @endif

                            @if ($complaint->verification_reason)
                                <p class="text-xs text-gray-500 mt-2">
                                    {{ $complaint->verification_reason }}
                                </p>
                            @endif

                        </div>

                    </div>


                    @if ($complaint->status !== 'Rejected')

                        @if ($isCommercial)

                            {{-- Initial Processing Started --}}
                            @php
                                $processingStarted = (bool) $commercialResolution?->started_at;
                            @endphp

                            <div class="flex gap-4">

                                <div class="flex flex-col items-center">

                                    <div
                                        class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                        {{ $processingStarted ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">

                                        <i class="fas {{ $processingStarted ? 'fa-check' : 'fa-circle' }} text-xs"></i>

                                    </div>

                                    <div
                                        class="w-px flex-1 min-h-12 {{ $processingStarted ? 'bg-green-200' : 'bg-gray-200' }}">
                                    </div>

                                </div>

                                <div class="pb-6">

                                    <p
                                        class="text-sm font-semibold {{ $processingStarted ? 'text-gray-900' : 'text-gray-400' }}">
                                        Initial Processing Started
                                    </p>

                                    @if ($processingStarted)
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $commercialResolution->started_at->format('M d, Y • h:i A') }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            Started by
                                            {{ $commercialResolution->processor?->full_name ?? 'Customer Service' }}
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-400 mt-1">
                                            Not started
                                        </p>
                                    @endif

                                </div>

                            </div>


                            {{-- Initial Processing Completed --}}
                            @php
                                $processingCompleted = (bool) $commercialResolution?->initial_processing_completed_at;
                            @endphp

                            <div class="flex gap-4">

                                <div class="flex flex-col items-center">

                                    <div
                                        class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                        {{ $processingCompleted ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">

                                        <i class="fas {{ $processingCompleted ? 'fa-check' : 'fa-circle' }} text-xs"></i>

                                    </div>

                                    <div
                                        class="w-px flex-1 min-h-12 {{ $processingCompleted ? 'bg-green-200' : 'bg-gray-200' }}">
                                    </div>

                                </div>

                                <div class="pb-6">

                                    <p
                                        class="text-sm font-semibold {{ $processingCompleted ? 'text-gray-900' : 'text-gray-400' }}">
                                        Initial Processing Completed
                                    </p>

                                    @if ($processingCompleted)
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $commercialResolution->initial_processing_completed_at->format('M d, Y • h:i A') }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            Processed by
                                            {{ $commercialResolution->processor?->full_name ?? 'Customer Service' }}
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-400 mt-1">
                                            Pending
                                        </p>
                                    @endif

                                </div>

                            </div>


                            @if ($isClosedWithoutMaintenance)

                                <div class="flex gap-4">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="w-9 h-9 rounded-full bg-green-100 text-green-700 flex items-center justify-center shrink-0">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>

                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold text-gray-900">
                                            Request Closed
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $complaint->completed_at?->format('M d, Y • h:i A') ?? $complaint->updated_at?->format('M d, Y • h:i A') }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            Closed after Customer Service processing
                                        </p>

                                    </div>

                                </div>
                            @else
                                {{-- Forwarded --}}
                                @php
                                    $forwarded =
                                        (bool) $commercialResolution?->forwarded_to_maintenance_at ||
                                        in_array(
                                            $complaint->status,
                                            ['For Maintenance', 'Assigned', 'In Progress', 'Completed', 'Closed'],
                                            true,
                                        );
                                @endphp

                                <div class="flex gap-4">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                            {{ $forwarded ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">

                                            <i class="fas {{ $forwarded ? 'fa-check' : 'fa-circle' }} text-xs"></i>

                                        </div>

                                        <div
                                            class="w-px flex-1 min-h-12 {{ $forwarded ? 'bg-green-200' : 'bg-gray-200' }}">
                                        </div>

                                    </div>

                                    <div class="pb-6">

                                        <p
                                            class="text-sm font-semibold {{ $forwarded ? 'text-gray-900' : 'text-gray-400' }}">
                                            Forwarded to Maintenance
                                        </p>

                                        @if ($forwarded)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $commercialResolution->forwarded_to_maintenance_at->format('M d, Y • h:i A') }}
                                            </p>

                                            <p class="text-xs text-gray-500 mt-1">
                                                Forwarded by
                                                {{ $commercialResolution->forwarder?->full_name ?? 'Customer Service' }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Pending Customer Service decision
                                            </p>
                                        @endif

                                    </div>

                                </div>

                            @endif
                        @else
                            {{-- Engineering automatically goes to Maintenance after verification --}}
                            <div class="flex gap-4">

                                <div class="flex flex-col items-center">

                                    <div
                                        class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                        {{ $complaint->verified_at ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">

                                        <i
                                            class="fas {{ $complaint->verified_at ? 'fa-check' : 'fa-circle' }} text-xs"></i>

                                    </div>

                                    <div
                                        class="w-px flex-1 min-h-12 {{ $complaint->verified_at ? 'bg-green-200' : 'bg-gray-200' }}">
                                    </div>

                                </div>

                                <div class="pb-6">

                                    <p
                                        class="text-sm font-semibold {{ $complaint->verified_at ? 'text-gray-900' : 'text-gray-400' }}">
                                        Forwarded to Maintenance
                                    </p>

                                    @if ($complaint->verified_at)
                                        <p class="text-xs text-gray-500 mt-1">
                                            Ready for Maintenance Management after verification
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-400 mt-1">
                                            Pending verification
                                        </p>
                                    @endif

                                </div>

                            </div>

                        @endif


                        @if (!$isClosedWithoutMaintenance)

                            {{-- Plumber Assigned --}}
                            @php
                                $assigned = $complaint->technicians->isNotEmpty();
                            @endphp

                            <div class="flex gap-4">

                                <div class="flex flex-col items-center">

                                    <div
                                        class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                        {{ $assigned ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">

                                        <i class="fas {{ $assigned ? 'fa-check' : 'fa-circle' }} text-xs"></i>

                                    </div>

                                    <div class="w-px flex-1 min-h-12 {{ $assigned ? 'bg-green-200' : 'bg-gray-200' }}">
                                    </div>

                                </div>

                                <div class="pb-6">

                                    <p class="text-sm font-semibold {{ $assigned ? 'text-gray-900' : 'text-gray-400' }}">
                                        Plumber Assigned
                                    </p>

                                    @if ($assigned)
                                        @if ($firstAssignedAt)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ \Illuminate\Support\Carbon::parse($firstAssignedAt)->format('M d, Y • h:i A') }}
                                            </p>
                                        @endif

                                        <p class="text-xs text-gray-500 mt-1">
                                            Assigned to {{ $assignedNames->join(', ') }}
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-400 mt-1">
                                            Waiting for Maintenance Management assignment
                                        </p>
                                    @endif

                                </div>

                            </div>


                            {{-- Maintenance Started --}}
                            @php
                                $maintenanceStarted = $report?->started_at ?: $firstStartedAt;
                            @endphp

                            <div class="flex gap-4">

                                <div class="flex flex-col items-center">

                                    <div
                                        class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                        {{ $maintenanceStarted ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">

                                        <i class="fas {{ $maintenanceStarted ? 'fa-check' : 'fa-circle' }} text-xs"></i>

                                    </div>

                                    <div
                                        class="w-px flex-1 min-h-12 {{ $maintenanceStarted ? 'bg-green-200' : 'bg-gray-200' }}">
                                    </div>

                                </div>

                                <div class="pb-6">

                                    <p
                                        class="text-sm font-semibold {{ $maintenanceStarted ? 'text-gray-900' : 'text-gray-400' }}">
                                        Maintenance Started
                                    </p>

                                    @if ($maintenanceStarted)
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ \Illuminate\Support\Carbon::parse($maintenanceStarted)->format('M d, Y • h:i A') }}
                                        </p>

                                        @if ($report?->technician)
                                            <p class="text-xs text-gray-500 mt-1">
                                                Started by {{ $report->technician->full_name }}
                                            </p>
                                        @endif
                                    @else
                                        <p class="text-xs text-gray-400 mt-1">
                                            Not started
                                        </p>
                                    @endif

                                </div>

                            </div>


                            {{-- Accomplished --}}
                            @php
                                $accomplished = (bool) $report?->submitted_at;
                            @endphp

                            <div class="flex gap-4">

                                <div class="flex flex-col items-center">

                                    <div
                                        class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                        {{ $accomplished ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">

                                        <i class="fas {{ $accomplished ? 'fa-check' : 'fa-circle' }} text-xs"></i>

                                    </div>

                                    <div
                                        class="w-px flex-1 min-h-12 {{ $accomplished ? 'bg-green-200' : 'bg-gray-200' }}">
                                    </div>

                                </div>

                                <div class="pb-6">

                                    <p
                                        class="text-sm font-semibold {{ $accomplished ? 'text-gray-900' : 'text-gray-400' }}">
                                        Maintenance Accomplished
                                    </p>

                                    @if ($accomplished)
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $report->submitted_at->format('M d, Y • h:i A') }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            Accomplished by
                                            {{ $report->technician?->full_name ?? ($assignedNames->first() ?? 'Assigned plumber') }}
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-400 mt-1">
                                            Pending
                                        </p>
                                    @endif

                                </div>

                            </div>


                            {{-- Manager Review --}}
                            @php
                                $reviewed = (bool) $report?->reviewed_at;
                            @endphp

                            <div class="flex gap-4">

                                <div class="flex flex-col items-center">

                                    <div
                                        class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                        {{ $reviewed ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">

                                        <i class="fas {{ $reviewed ? 'fa-check' : 'fa-circle' }} text-xs"></i>

                                    </div>

                                    <div class="w-px flex-1 min-h-12 {{ $reviewed ? 'bg-green-200' : 'bg-gray-200' }}">
                                    </div>

                                </div>

                                <div class="pb-6">

                                    <p class="text-sm font-semibold {{ $reviewed ? 'text-gray-900' : 'text-gray-400' }}">
                                        Manager Review
                                    </p>

                                    @if ($reviewed)
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $report->reviewed_at->format('M d, Y • h:i A') }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $report->review_status }}
                                            @if ($report->reviewer)
                                                by {{ $report->reviewer->full_name }}
                                            @endif
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $report?->review_status === 'Pending Review' ? 'Awaiting manager review' : 'Pending' }}
                                        </p>
                                    @endif

                                </div>

                            </div>


                            {{-- Closed --}}
                            @php
                                $maintenanceClosed = $complaint->status === 'Closed';
                            @endphp

                            <div class="flex gap-4">

                                <div class="flex flex-col items-center">

                                    <div
                                        class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                        {{ $maintenanceClosed ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">

                                        <i class="fas {{ $maintenanceClosed ? 'fa-check' : 'fa-circle' }} text-xs"></i>

                                    </div>

                                </div>

                                <div>

                                    <p
                                        class="text-sm font-semibold {{ $maintenanceClosed ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ $isCommercial ? 'Request Closed' : 'Complaint Closed' }}
                                    </p>

                                    @if ($maintenanceClosed)
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $complaint->completed_at?->format('M d, Y • h:i A') ?? $complaint->updated_at?->format('M d, Y • h:i A') }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            Finalized by Maintenance Management
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-400 mt-1">
                                            Pending finalization
                                        </p>
                                    @endif

                                </div>

                            </div>

                        @endif

                    @endif

                </div>

            </div>

        </div>


        {{-- AI Assisted Analysis --}}
        @if ($aiAnalysis)
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">

                <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

                    <div class="flex items-center justify-between gap-3">

                        <div>
                            <h2 class="font-semibold text-gray-900">
                                AI-Assisted Analysis
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Supporting information for Customer Service review.
                            </p>
                        </div>

                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $urgencyClasses }}">
                            {{ $urgencyLabel }} Urgency
                        </span>

                    </div>

                </div>

                <div class="p-5 sm:p-6 space-y-5">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs text-gray-500">Consumer Submitted</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $consumerCategory?->name ?? 'Not available' }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-violet-200 bg-violet-50 p-4">
                            <p class="text-xs text-violet-600">AI Suggested</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $predictedCategory?->name ?? ($aiAnalysis->predicted_type ?? 'Not available') }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                            <p class="text-xs text-green-600">Verified Classification</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $verifiedCategory?->name ?? ($complaint->status === 'Pending' ? 'Pending review' : $complaint->category?->name ?? 'Not available') }}
                            </p>
                        </div>

                    </div>

                    @if ($supportingSummary)
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-2">
                                AI Summary
                            </p>

                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                                {{ $supportingSummary }}
                            </div>
                        </div>
                    @endif

                    @if (!empty($supportingIndicators) || !empty($urgencyReasons))
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                            @if (!empty($supportingIndicators))
                                <div>
                                    <p class="text-xs font-medium text-gray-500 mb-2">
                                        Classification Indicators
                                    </p>

                                    <ul class="space-y-2 text-sm text-gray-700">
                                        @foreach ($supportingIndicators as $indicator)
                                            <li class="flex gap-2">
                                                <i class="fas fa-circle text-[6px] mt-2 text-gray-400"></i>
                                                <span>{{ is_array($indicator) ? data_get($indicator, 'text', json_encode($indicator)) : $indicator }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (!empty($urgencyReasons))
                                <div>
                                    <p class="text-xs font-medium text-gray-500 mb-2">
                                        Urgency Indicators
                                    </p>

                                    <ul class="space-y-2 text-sm text-gray-700">
                                        @foreach ($urgencyReasons as $reason)
                                            <li class="flex gap-2">
                                                <i class="fas fa-circle text-[6px] mt-2 text-gray-400"></i>
                                                <span>{{ is_array($reason) ? data_get($reason, 'text', json_encode($reason)) : $reason }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        </div>
                    @endif

                </div>

            </div>
        @endif

        {{-- Related Complaint Analysis --}}
{{-- Related Complaint Analysis --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

    <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <div class="flex items-start gap-3">

                <div
                    class="w-9 h-9 rounded-xl bg-violet-50 text-violet-600
                    flex items-center justify-center shrink-0">

                    <i class="fas fa-link text-sm"></i>

                </div>

                <div>

                    <h2 class="font-semibold text-gray-900">
                        Related Complaint Analysis
                    </h2>

                    <p class="text-sm text-gray-500 mt-0.5">
                        AI compares nearby and recent complaints that may be part of the same or a connected incident.
                    </p>

                </div>

            </div>

            @if (($similarComplaints['count'] ?? 0) > 0)

                <span
                    class="inline-flex self-start sm:self-auto items-center gap-1.5
                    px-2.5 py-1 rounded-full
                    bg-violet-50 text-violet-700
                    border border-violet-100
                    text-xs font-semibold">

                    <i class="fas fa-link text-[10px]"></i>

                    {{ $similarComplaints['count'] }}

                    {{ ($similarComplaints['count'] ?? 0) === 1
                        ? 'Possible Match'
                        : 'Possible Matches' }}

                </span>

            @endif

        </div>

    </div>


    <div class="p-5 sm:p-6">

        @if ($similarComplaintError)

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">

                <div class="flex items-start gap-3">

                    <i class="fas fa-triangle-exclamation text-amber-600 mt-0.5"></i>

                    <div>

                        <p class="text-sm font-semibold text-amber-800">
                            Related complaint analysis unavailable
                        </p>

                        <p class="text-sm text-amber-700 mt-1">
                            {{ $similarComplaintError }}
                        </p>

                    </div>

                </div>

            </div>

        @elseif (
            ($similarComplaints['has_possible_related_complaints'] ?? false) &&
            !empty($similarComplaints['matches'])
        )

            <div class="space-y-4">

                @foreach ($similarComplaints['matches'] as $match)

                    @php
                        $relatedComplaintId = isset($match['complaint_id'])
                            ? (int) $match['complaint_id']
                            : null;

                        $relationship =
                            $match['relationship'] ?? 'Possibly Related';

                        $scorePercentage = (int) round(
                            (float) ($match['score_percentage'] ?? 0)
                        );

                        $textSimilarityPercentage = (int) round(
                            (float) ($match['text_similarity_percentage'] ?? 0)
                        );

                        $locationSimilarityPercentage = (int) round(
                            (float) ($match['location_similarity_percentage'] ?? 0)
                        );

                        $typeSimilarityPercentage = (int) round(
                            (float) ($match['type_similarity_percentage'] ?? 0)
                        );

                        $timeSimilarityPercentage = (int) round(
                            (float) ($match['time_similarity_percentage'] ?? 0)
                        );

                        $consumerSimilarityPercentage = (int) round(
                            (float) ($match['consumer_score_percentage'] ?? 0)
                        );

                        $distanceKm = $match['distance_km'] ?? null;

                        $hoursDifference = $match['hours_difference'] ?? null;

                        $sameComplaintType =
                            !empty($match['same_complaint_type']);

                        $consumerMatchAvailable =
                            !empty($match['consumer_match_available']);

                        $sameConsumer =
                            !empty($match['same_consumer']);

                        $evidence = is_array($match['evidence'] ?? null)
                            ? $match['evidence']
                            : [];

                        $relationshipClasses = match ($relationship) {
                            'Likely Related' =>
                                'bg-red-50 text-red-700 border-red-200',

                            'Possibly Related' =>
                                'bg-amber-50 text-amber-700 border-amber-200',

                            default =>
                                'bg-gray-50 text-gray-700 border-gray-200',
                        };

                        $relatedStatus = $match['status'] ?? null;

                        $relatedStatusLabel = match ($relatedStatus) {
                            'Completed' => 'Accomplished',
                            default => $relatedStatus,
                        };

                        $relatedStatusClasses = match ($relatedStatus) {
                            'Pending' =>
                                'bg-amber-50 text-amber-700',

                            'Verified' =>
                                'bg-green-50 text-green-700',

                            'CS Processing' =>
                                'bg-sky-50 text-sky-700',

                            'For Maintenance' =>
                                'bg-violet-50 text-violet-700',

                            'Assigned' =>
                                'bg-indigo-50 text-indigo-700',

                            'In Progress' =>
                                'bg-blue-50 text-blue-700',

                            'Completed' =>
                                'bg-emerald-50 text-emerald-700',

                            'Closed' =>
                                'bg-slate-100 text-slate-700',

                            'Rejected' =>
                                'bg-red-50 text-red-700',

                            default =>
                                'bg-gray-100 text-gray-600',
                        };
                    @endphp


                    <div
                        class="rounded-2xl border border-gray-200
                        bg-white overflow-hidden">

                        <div class="p-4 sm:p-5">

                            <div
                                class="flex flex-col sm:flex-row
                                sm:items-start sm:justify-between gap-4">

                                <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        @if ($relatedComplaintId)

                                            <a
                                                href="{{ route(
                                                    'customer-service.complaints.show',
                                                    $relatedComplaintId
                                                ) }}"
                                                class="inline-flex items-center gap-1.5
                                                text-sm font-bold text-gray-900
                                                hover:text-blue-600 transition">

                                                {{ $match['complaint_no'] ?? 'Unknown Complaint' }}

                                                <i
                                                    class="fas fa-arrow-up-right-from-square
                                                    text-[10px] text-gray-400">
                                                </i>

                                            </a>

                                        @else

                                            <span class="text-sm font-bold text-gray-900">
                                                {{ $match['complaint_no'] ?? 'Unknown Complaint' }}
                                            </span>

                                        @endif


                                        @if ($relatedStatus)

                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-full
                                                text-[11px] font-medium
                                                {{ $relatedStatusClasses }}">

                                                {{ $relatedStatusLabel }}

                                            </span>

                                        @endif

                                    </div>


                                    <div class="mt-2">

                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full
                                            border text-xs font-semibold
                                            {{ $relationshipClasses }}">

                                            {{ $relationship }}

                                        </span>

                                    </div>

                                </div>


                                <div class="sm:text-right shrink-0">

                                    <p class="text-[11px] text-gray-400">
                                        Relationship Score
                                    </p>

                                    <p class="text-2xl font-bold text-gray-900">
                                        {{ $scorePercentage }}%
                                    </p>

                                </div>

                            </div>


                            <div
                                class="grid grid-cols-2
                                md:grid-cols-3 lg:grid-cols-5
                                gap-3 mt-5">

                                <div class="rounded-xl bg-gray-50 p-3">

                                    <div class="flex items-center justify-between gap-2">

                                        <p class="text-[11px] text-gray-500">
                                            Location
                                        </p>

                                    </div>

                                    <p class="mt-1 text-sm font-semibold text-gray-900">

                                        @if ($distanceKm !== null)

                                            {{ number_format((float) $distanceKm, 2) }} km away

                                        @else

                                            Not available

                                        @endif

                                    </p>

                                    @if ($distanceKm !== null)

                                        <p class="mt-1 text-[11px] text-gray-400">
                                            {{ $locationSimilarityPercentage }}% proximity score
                                        </p>

                                    @endif

                                </div>


                                <div class="rounded-xl bg-gray-50 p-3">

                                    <div class="flex items-center justify-between">

                                        <p class="text-[11px] text-gray-500">
                                            Description
                                        </p>



                                    </div>

                                    <p class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ $textSimilarityPercentage }}%
                                    </p>

                                    <p class="mt-1 text-[11px] text-gray-400">
                                        Text similarity
                                    </p>

                                </div>


                                <div class="rounded-xl bg-gray-50 p-3">

                                    <div class="flex items-center justify-between gap-2">

                                        <p class="text-[11px] text-gray-500">
                                            Complaint Type
                                        </p>


                                    </div>

                                    <p class="mt-1 text-sm font-semibold text-gray-900">

                                        {{ $sameComplaintType
                                            ? 'Same type'
                                            : 'Different type' }}

                                    </p>

                                    <p class="mt-1 text-[11px] text-gray-400">

                                        {{ $sameComplaintType
                                            ? $typeSimilarityPercentage . '% type match'
                                            : 'Can still be related' }}

                                    </p>

                                </div>


                                <div class="rounded-xl bg-gray-50 p-3">

                                    <div class="flex items-center justify-between gap-2">

                                        <p class="text-[11px] text-gray-500">
                                            Time
                                        </p>


                                    </div>

                                    @if ($hoursDifference !== null)

                                        <p class="mt-1 text-sm font-semibold text-gray-900">

                                            @if ((float) $hoursDifference < 1)

                                                Less than 1 hour apart

                                            @elseif ((float) $hoursDifference == 1)

                                                1 hour apart

                                            @else

                                                {{ number_format(
                                                    (float) $hoursDifference,
                                                    1
                                                ) }} hours apart

                                            @endif

                                        </p>

                                        <p class="mt-1 text-[11px] text-gray-400">
                                            {{ $timeSimilarityPercentage }}% time score
                                        </p>

                                    @else

                                        <p class="mt-1 text-sm font-semibold text-gray-900">
                                            Not available
                                        </p>

                                    @endif

                                </div>


                                <div
                                    class="rounded-xl bg-gray-50 p-3
                                    col-span-2 md:col-span-1">

                                    <div class="flex items-center justify-between gap-2">

                                        <p class="text-[11px] text-gray-500">
                                            Consumer
                                        </p>



                                    </div>

                                    @if ($consumerMatchAvailable)

                                        <p class="mt-1 text-sm font-semibold text-gray-900">
                                            {{ $sameConsumer
                                                ? 'Same account'
                                                : 'Different account' }}
                                        </p>

                                        <p class="mt-1 text-[11px] text-gray-400">
                                            {{ $consumerSimilarityPercentage }}% consumer score
                                        </p>

                                    @else

                                        <p class="mt-1 text-sm font-semibold text-gray-900">
                                            Not available
                                        </p>

                                    @endif

                                </div>

                            </div>


                            @if (!$sameComplaintType)

                                <div
                                    class="mt-4 rounded-xl
                                    border border-blue-100
                                    bg-blue-50 px-3 py-2.5">

                                    <div class="flex items-start gap-2">

                                        <i
                                            class="fas fa-circle-info
                                            text-blue-600 text-xs mt-0.5">
                                        </i>

                                        <p class="text-xs text-blue-700 leading-relaxed">
                                            Different complaint types may still describe the same
                                            water-service incident, such as Mainline Leakage,
                                            No Water, and Low Water Pressure.
                                        </p>

                                    </div>

                                </div>

                            @endif


                            @if (!empty($evidence))

                                <div class="mt-4 pt-4 border-t border-gray-100">

                                    <p
                                        class="text-[11px] font-semibold uppercase
                                        tracking-wide text-gray-400">

                                        Supporting Evidence

                                    </p>

                                    <div class="mt-2 space-y-2">

                                        @foreach ($evidence as $item)

                                            <div
                                                class="flex items-start gap-2
                                                text-xs text-gray-600">

                                                <i
                                                    class="fas fa-check
                                                    text-green-500 mt-0.5 shrink-0">
                                                </i>

                                                <span>
                                                    {{ $item }}
                                                </span>

                                            </div>

                                        @endforeach

                                    </div>

                                </div>

                            @endif


                            @if (!empty($match['human_confirmation_required']))

                                <div
                                    class="mt-4 flex items-start gap-2
                                    rounded-xl bg-slate-50
                                    border border-slate-200
                                    px-3 py-2.5">

                                    <i
                                        class="fas fa-user-check
                                        text-slate-500 text-xs mt-0.5">
                                    </i>

                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        AI provides relationship support only.
                                        Customer Service should review the complaints
                                        before confirming that they belong to the same
                                        or connected incident.
                                    </p>

                                </div>

                            @endif


                            @if ($relatedComplaintId)

                                <div class="mt-4 pt-4 border-t border-gray-100">

                                    <a
                                        href="{{ route(
                                            'customer-service.complaints.show',
                                            $relatedComplaintId
                                        ) }}"
                                        class="inline-flex w-full sm:w-auto
                                        items-center justify-center gap-2
                                        px-4 py-2.5 rounded-xl
                                        border border-gray-200 bg-white
                                        text-sm font-semibold text-gray-700
                                        hover:border-blue-200 hover:bg-blue-50
                                        hover:text-blue-700 transition">

                                        <i class="far fa-eye"></i>

                                        View Complaint

                                        <i class="fas fa-arrow-right text-xs"></i>

                                    </a>

                                </div>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="py-6">

                <div class="flex flex-col items-center text-center">

                    <div
                        class="w-10 h-10 rounded-xl
                        bg-gray-50 text-gray-400
                        flex items-center justify-center">

                        <i class="fas fa-link-slash text-sm"></i>

                    </div>

                    <p class="mt-3 text-sm font-semibold text-gray-700">
                        No related complaints detected
                    </p>

                    <p class="mt-1 text-xs text-gray-500 max-w-md">
                        No recent complaint reached the relationship threshold
                        based on location, description, complaint type, time,
                        and consumer information when available.
                    </p>

                </div>

            </div>

        @endif

    </div>

</div>


        {{-- Pending Verification --}}
        @if ($complaint->status === 'Pending')
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">

                <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

                    <h2 class="font-semibold text-gray-900">
                        Review & Decision
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Confirm or correct the classification before verification.
                    </p>

                </div>

                <div class="p-5 sm:p-6">

                    <form id="verifyComplaintForm" method="POST"
                        action="{{ route('customer-service.complaints.verify', $complaint) }}"
                        onsubmit="return confirm('Verify this complaint with the selected division and complaint type?');">

                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <div>

                                <label for="verification_division_id"
                                    class="block text-sm font-medium text-gray-700 mb-2">

                                    Verified Division
                                    <span class="text-red-500">*</span>

                                </label>

                                <select name="division_id" id="verification_division_id" required
                                    class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                                    <option value="">Select division</option>

                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}" @selected((string) old('division_id', $complaint->division_id) === (string) $division->id)>

                                            {{ $division->name }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            <div>

                                <label for="verification_complaint_category_id"
                                    class="block text-sm font-medium text-gray-700 mb-2">

                                    Verified Complaint Type
                                    <span class="text-red-500">*</span>

                                </label>

                                <select name="complaint_category_id" id="verification_complaint_category_id" required
                                    class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                                    <option value="">Select complaint type</option>

                                </select>

                            </div>

                        </div>


                        <div class="mt-5">

                            <label for="verification_reason" class="block text-sm font-medium text-gray-700 mb-2">

                                Verification Notes
                                <span class="font-normal text-gray-400">(optional)</span>

                            </label>

                            <textarea name="verification_reason" id="verification_reason" rows="3" maxlength="2000"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Add notes about the verification or classification correction...">{{ old('verification_reason') }}</textarea>

                        </div>


                        <div
                            class="mt-6 pt-5 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                            <button type="button" id="showRejectComplaint"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                                border border-red-200 text-sm font-medium text-red-600 hover:bg-red-50">

                                <i class="fas fa-ban"></i>
                                Reject

                            </button>

                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                                bg-green-600 text-sm font-semibold text-white hover:bg-green-700">

                                <i class="fas fa-circle-check"></i>
                                Verify Complaint

                            </button>

                        </div>

                    </form>


                    <div id="rejectComplaintPanel" class="hidden mt-5 pt-5 border-t border-gray-100">

                        <form id="rejectComplaintForm" method="POST"
                            action="{{ route('customer-service.complaints.reject', $complaint) }}"
                            onsubmit="return confirm('Reject this complaint?');">

                            @csrf

                            <div class="rounded-xl border border-red-200 bg-red-50 p-4">

                                <label for="rejection_reason" class="block text-sm font-medium text-red-900 mb-2">

                                    Rejection Reason
                                    <span class="text-red-600">*</span>

                                </label>

                                <textarea name="verification_reason" id="rejection_reason" rows="3" maxlength="2000" required
                                    class="w-full rounded-xl border-red-200 bg-white text-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Explain why this complaint is being rejected...">{{ old('verification_reason') }}</textarea>

                                <div class="mt-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">

                                    <button type="button" id="cancelRejectComplaint"
                                        class="px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">

                                        Cancel

                                    </button>

                                    <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                                        bg-red-600 text-sm font-semibold text-white hover:bg-red-700">

                                        <i class="fas fa-ban"></i>
                                        Confirm Rejection

                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        @endif


        {{-- Customer Service Processing --}}
        @if ($isCommercial && $complaint->status === 'Verified')
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">

                <div class="p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                    <div>
                        <h2 class="font-semibold text-gray-900">
                            Customer Service Processing
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Start the initial processing required for this service request.
                        </p>
                    </div>

                    <form method="POST"
                        action="{{ route('customer-service.complaints.commercial.start', $complaint) }}"
                        onsubmit="return confirm('Start initial processing for this service request?');">

                        @csrf

                        <button type="submit"
                            class="w-full lg:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                    bg-blue-600 text-sm font-semibold text-white hover:bg-blue-700">

                            <i class="fas fa-play"></i>
                            Start Initial Processing

                        </button>

                    </form>

                </div>

            </div>
        @endif


        @if ($isCommercial && $complaint->status === 'CS Processing')

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">

                <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

                    <h2 class="font-semibold text-gray-900">
                        Customer Service Processing
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Record the result of the initial processing and determine the next step.
                    </p>

                </div>


                @if (!$commercialResolution?->initial_processing_completed_at)
                    <form id="commercialResolutionForm" method="POST"
                        action="{{ route('customer-service.complaints.commercial.resolution', $complaint) }}">

                        @csrf
                        @method('PUT')

                        <div class="p-5 sm:p-6 space-y-5">

                            <div>

                                <label for="findings" class="block text-sm font-semibold text-gray-700 mb-2">

                                    Findings
                                    <span class="text-red-500">*</span>

                                </label>

                                <textarea name="findings" id="findings" rows="5" maxlength="5000" required
                                    class="w-full rounded-xl border-gray-300 text-sm
                            focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Record the result of the initial review, inspection, or assessment...">{{ old('findings', $commercialResolution?->findings) }}</textarea>

                            </div>


                            <div>

                                <label for="resolution_remarks" class="block text-sm font-semibold text-gray-700 mb-2">

                                    Resolution / Recommendation
                                    <span class="text-red-500">*</span>

                                </label>

                                <textarea name="resolution_remarks" id="resolution_remarks" rows="5" maxlength="5000" required
                                    class="w-full rounded-xl border-gray-300 text-sm
                            focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Record the resolution or recommended next step...">{{ old('resolution_remarks', $commercialResolution?->resolution_remarks) }}</textarea>

                            </div>


                            <div
                                class="pt-5 border-t border-gray-100
                        flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                                <button type="submit" onclick="prepareCommercialSave()"
                                    class="px-4 py-2.5 rounded-xl
                            border border-gray-300 bg-white
                            text-sm font-semibold text-gray-700
                            hover:bg-gray-50">

                                    Save

                                </button>

                                <button type="submit" onclick="return prepareCommercialComplete()"
                                    class="inline-flex items-center justify-center gap-2
                            px-5 py-2.5 rounded-xl
                            bg-blue-600 text-sm font-semibold text-white
                            hover:bg-blue-700">

                                    <i class="fas fa-circle-check"></i>
                                    Complete Initial Processing

                                </button>

                            </div>

                        </div>

                    </form>
                @else
                    <div class="p-5 sm:p-6 space-y-5">

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                            <div>

                                <p class="text-xs font-medium text-gray-500 mb-2">
                                    Findings
                                </p>

                                <div
                                    class="rounded-xl border border-gray-200
                            bg-gray-50 p-4 text-sm text-gray-700
                            whitespace-pre-line">

                                    {{ $commercialResolution->findings }}

                                </div>

                            </div>


                            <div>

                                <p class="text-xs font-medium text-gray-500 mb-2">
                                    Resolution / Recommendation
                                </p>

                                <div
                                    class="rounded-xl border border-gray-200
                            bg-gray-50 p-4 text-sm text-gray-700
                            whitespace-pre-line">

                                    {{ $commercialResolution->resolution_remarks }}

                                </div>

                            </div>

                        </div>


                        <div class="pt-5 border-t border-gray-100">

                            <p class="text-sm font-semibold text-gray-900">
                                Initial Processing Completed
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                {{ $commercialResolution->initial_processing_completed_at?->format('M d, Y h:i A') }}
                            </p>

                            <p class="text-sm text-gray-500 mt-2">
                                Choose the next step based on the findings above.
                            </p>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                            <form method="POST"
                                action="{{ route('customer-service.complaints.commercial.close', $complaint) }}"
                                onsubmit="return confirm('Close this request? No maintenance assignment will be created.');">

                                @csrf

                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2
                            px-5 py-3 rounded-xl
                            border border-gray-300 bg-white
                            font-semibold text-gray-700
                            hover:bg-gray-50">

                                    <i class="fas fa-check"></i>
                                    Close Request

                                </button>

                            </form>


                            <form method="POST"
                                action="{{ route('customer-service.complaints.commercial.forward-maintenance', $complaint) }}"
                                onsubmit="return confirm('Forward this request to Maintenance for plumber assignment?');">

                                @csrf

                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2
                            px-5 py-3 rounded-xl
                            bg-violet-600 font-semibold text-white
                            hover:bg-violet-700">

                                    <i class="fas fa-share"></i>
                                    Forward to Maintenance

                                </button>

                            </form>

                        </div>

                    </div>
                @endif

            </div>

        @endif


        {{-- Read-only CS Processing Result --}}
        @if (
            $isCommercial &&
                $commercialResolution &&
                $commercialResolution->initial_processing_completed_at &&
                $complaint->status !== 'CS Processing')
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">

                <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

                    <h2 class="font-semibold text-gray-900">
                        Customer Service Processing Result
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Findings recorded before the request was closed or forwarded.
                    </p>

                </div>

                <div class="p-5 sm:p-6 grid grid-cols-1 lg:grid-cols-2 gap-5">

                    <div>

                        <p class="text-xs font-medium text-gray-500 mb-2">
                            Findings
                        </p>

                        <div
                            class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 whitespace-pre-line">
                            {{ $commercialResolution->findings ?: 'No findings recorded.' }}
                        </div>

                    </div>

                    <div>

                        <p class="text-xs font-medium text-gray-500 mb-2">
                            Resolution / Recommendation
                        </p>

                        <div
                            class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 whitespace-pre-line">
                            {{ $commercialResolution->resolution_remarks ?: 'No recommendation recorded.' }}
                        </div>

                    </div>

                </div>

            </div>
        @endif


        {{-- Maintenance Result --}}
        @if ($report)
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">

                <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                        <div>

                            <h2 class="font-semibold text-gray-900">
                                Maintenance Result
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Accomplishment information submitted by the assigned plumber.
                            </p>

                        </div>

                        <span
                            class="inline-flex self-start sm:self-auto px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $report->review_status === 'Approved'
                                ? 'bg-green-50 text-green-700'
                                : ($report->review_status === 'Returned'
                                    ? 'bg-red-50 text-red-700'
                                    : 'bg-amber-50 text-amber-700') }}">

                            {{ $report->review_status }}

                        </span>

                    </div>

                </div>

                <div class="p-5 sm:p-6 space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                        <div>
                            <p class="text-xs text-gray-500">Plumber</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $report->technician?->full_name ?? ($assignedNames->first() ?? '—') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Maintenance Started</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $report->started_at?->format('M d, Y h:i A') ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Accomplished</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $report->submitted_at?->format('M d, Y h:i A') ?? '—' }}
                            </p>
                        </div>

                    </div>


                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                        <div>

                            <p class="text-sm font-semibold text-gray-900 mb-2">
                                Diagnosis / Findings
                            </p>

                            <div
                                class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 whitespace-pre-line">
                                {{ $report->diagnosis ?: 'No diagnosis recorded.' }}
                            </div>

                        </div>

                        <div>

                            <p class="text-sm font-semibold text-gray-900 mb-2">
                                Root Cause
                            </p>

                            <div
                                class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 whitespace-pre-line">
                                {{ $report->root_cause ?: 'No root cause recorded.' }}
                            </div>

                        </div>

                    </div>


                    <div>

                        <p class="text-sm font-semibold text-gray-900 mb-2">
                            Materials / Parts
                        </p>

                        <div
                            class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->materials_parts ?: 'None recorded.' }}
                        </div>

                    </div>


                    @if ($report->technician_notes)
                        <div>

                            <p class="text-sm font-semibold text-gray-900 mb-2">
                                Plumber Notes
                            </p>

                            <div
                                class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 whitespace-pre-line">
                                {{ $report->technician_notes }}
                            </div>

                        </div>
                    @endif


                    @if ($report->before_photo || $report->after_photo)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            @if ($report->before_photo)
                                <div>

                                    <p class="text-sm font-semibold text-gray-900 mb-2">
                                        Before Maintenance
                                    </p>

                                    <img src="{{ asset('storage/' . $report->before_photo) }}" alt="Before maintenance"
                                        class="w-full max-h-80 object-cover rounded-xl border border-gray-200">

                                </div>
                            @endif

                            @if ($report->after_photo)
                                <div>

                                    <p class="text-sm font-semibold text-gray-900 mb-2">
                                        After Maintenance
                                    </p>

                                    <img src="{{ asset('storage/' . $report->after_photo) }}" alt="After maintenance"
                                        class="w-full max-h-80 object-cover rounded-xl border border-gray-200">

                                </div>
                            @endif

                        </div>
                    @endif


                    @if ($report->review_remarks)
                        <div class="border-t border-gray-100 pt-5">

                            <p class="text-sm font-semibold text-gray-900 mb-2">
                                Manager Review Remarks
                            </p>

                            <div
                                class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 whitespace-pre-line">
                                {{ $report->review_remarks }}
                            </div>

                        </div>
                    @endif

                </div>

            </div>
        @endif


        {{-- Delete pending CS-created complaint --}}
        @if ($canEdit)
            <div class="bg-white border border-red-200 rounded-2xl shadow-sm">

                <div class="p-5 sm:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div>

                        <p class="font-semibold text-gray-900">
                            Delete Complaint
                        </p>

                        <p class="text-sm text-gray-500 mt-1">
                            Only the Customer Service employee who created this pending complaint can delete it.
                        </p>

                    </div>

                    <form action="{{ route('customer-service.complaints.destroy', $complaint) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this complaint?');">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            class="w-full md:w-auto inline-flex items-center justify-center gap-2
                            px-4 py-2.5 rounded-xl bg-red-600 text-sm font-semibold text-white hover:bg-red-700">

                            <i class="fas fa-trash"></i>
                            Delete

                        </button>

                    </form>

                </div>

            </div>
        @endif

    </div>


    <script>
        function prepareCommercialSave() {
            const form = document.getElementById('commercialResolutionForm');

            if (!form) {
                return;
            }

            form.action = @json(route('customer-service.complaints.commercial.resolution', $complaint));

            let methodInput = form.querySelector('input[name="_method"]');

            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }

            methodInput.value = 'PUT';
        }


        function prepareCommercialComplete() {
            const form = document.getElementById('commercialResolutionForm');

            if (!form) {
                return false;
            }

            const findings = document.getElementById('findings');
            const resolution = document.getElementById('resolution_remarks');

            if (!findings || !findings.value.trim()) {
                alert('Findings are required before completing initial processing.');

                if (findings) {
                    findings.focus();
                }

                return false;
            }

            if (!resolution || !resolution.value.trim()) {
                alert('Resolution / Recommendation is required before completing initial processing.');

                if (resolution) {
                    resolution.focus();
                }

                return false;
            }

            if (!confirm(
                    'Complete the initial Customer Service processing?\n\n' +
                    'After completion, choose Close Request or Forward to Maintenance.'
                )) {
                return false;
            }

            form.action = @json(route('customer-service.complaints.commercial.complete', $complaint));

            const methodInput = form.querySelector('input[name="_method"]');

            if (methodInput) {
                methodInput.remove();
            }

            return true;
        }


        document.addEventListener('DOMContentLoaded', function() {
            const divisionSelect = document.getElementById('verification_division_id');
            const categorySelect = document.getElementById('verification_complaint_category_id');

            if (divisionSelect && categorySelect) {
                const divisions = @json($verificationDivisions);

                const originalCategoryId =
                    @json((string) old('complaint_category_id', $complaint->complaint_category_id));

                function loadComplaintTypes(divisionId, selectedCategoryId = null) {
                    categorySelect.innerHTML =
                        '<option value="">Select complaint type</option>';

                    if (!divisionId) {
                        return;
                    }

                    const selectedDivision = divisions.find(function(division) {
                        return String(division.id) === String(divisionId);
                    });

                    if (!selectedDivision) {
                        return;
                    }

                    selectedDivision.complaint_types.forEach(function(type) {
                        const option = document.createElement('option');

                        option.value = type.id;
                        option.textContent = type.name;

                        if (
                            selectedCategoryId !== null &&
                            selectedCategoryId !== '' &&
                            String(type.id) === String(selectedCategoryId)
                        ) {
                            option.selected = true;
                        }

                        categorySelect.appendChild(option);
                    });
                }

                loadComplaintTypes(
                    divisionSelect.value,
                    originalCategoryId
                );

                divisionSelect.addEventListener('change', function() {
                    loadComplaintTypes(this.value);
                });
            }


            const showRejectButton = document.getElementById('showRejectComplaint');
            const cancelRejectButton = document.getElementById('cancelRejectComplaint');
            const rejectPanel = document.getElementById('rejectComplaintPanel');
            const rejectionReason = document.getElementById('rejection_reason');

            if (showRejectButton && rejectPanel) {
                showRejectButton.addEventListener('click', function() {
                    rejectPanel.classList.remove('hidden');
                    showRejectButton.classList.add('hidden');

                    setTimeout(function() {
                        if (rejectionReason) {
                            rejectionReason.focus();
                        }
                    }, 100);
                });
            }

            if (cancelRejectButton && rejectPanel) {
                cancelRejectButton.addEventListener('click', function() {
                    rejectPanel.classList.add('hidden');

                    if (showRejectButton) {
                        showRejectButton.classList.remove('hidden');
                    }
                });
            }
        });
    </script>


    @if (!$isCommercial && $complaint->latitude && $complaint->longitude)
        @push('styles')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        @endpush

        @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const mapElement = document.getElementById('complaint-map');

                    if (!mapElement) {
                        return;
                    }

                    const latitude = {{ (float) $complaint->latitude }};
                    const longitude = {{ (float) $complaint->longitude }};

                    const map = L.map('complaint-map', {
                        zoomControl: true,
                        attributionControl: true
                    });

                    L.tileLayer(
                        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap contributors'
                        }
                    ).addTo(map);

                    map.setView([latitude, longitude], 17);

                    L.marker([latitude, longitude])
                        .addTo(map)
                        .bindPopup(
                            '<strong>{{ addslashes($complaint->complaint_no) }}</strong><br>' +
                            '{{ addslashes($complaint->category?->name ?? 'Water Service Concern') }}'
                        );

                    setTimeout(function() {
                        map.invalidateSize();
                    }, 200);
                });
            </script>
        @endpush
    @endif

@endsection
