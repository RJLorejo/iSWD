 @extends('layouts.report')

@section('title', 'Customer Service Reports & Analytics')

@section('report-title')
    Customer Service Reports &amp; Analytics
@endsection

@section('report-subtitle')
    Complaint Performance, AI Urgency Assessment, and Verification Summary
@endsection

@section('report-period')
    @if (!empty($dateFrom) || !empty($dateTo))
        Reporting Period:
        {{ !empty($dateFrom) ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : 'Beginning' }}
        —
        {{ !empty($dateTo) ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : 'Present' }}
    @else
        Reporting Period: {{ ucfirst($period ?? 'All Records') }}
    @endif
@endsection

@section('prepared-by-role', 'Customer Service')

@section('back-url')
    {{ route('customer-service.reports.index', request()->query()) }}
@endsection

@section('report-content')

@php
    /*
    |--------------------------------------------------------------------------
    | Safe display helpers
    |--------------------------------------------------------------------------
    | These helpers only format values for printing. The controller remains
    | responsible for calculating the actual report statistics.
    */

    $printNumber = fn ($value) => number_format((float) ($value ?? 0));

    $printPercent = fn ($value) => number_format((float) ($value ?? 0), 1) . '%';

    $printValue = fn ($value) => filled($value) ? $value : '—';

    $printUrgency = function ($analysis) {
        if (!$analysis) {
            return 'No AI Analysis';
        }

        $verified = trim((string) ($analysis->verified_urgency_level ?? ''));

        if ($verified !== '') {
            return $verified;
        }

        return 'Pending Verification';
    };

    $printAiUrgency = function ($analysis) {
        $urgency = trim((string) ($analysis->urgency_level ?? ''));

        return $urgency !== '' ? $urgency : 'Not Assessed';
    };

    $printVerificationStatus = function ($analysis) {
        if (!$analysis) {
            return 'No AI Analysis';
        }

        if (blank($analysis->urgency_level)) {
            return 'AI Not Assessed';
        }

        if (blank($analysis->verified_urgency_level)) {
            return 'Pending Verification';
        }

        if (strcasecmp(
            trim((string) $analysis->urgency_level),
            trim((string) $analysis->verified_urgency_level)
        ) === 0) {
            return 'Verified — Unchanged';
        }

        return 'Verified — Changed';
    };

    $printRecords = $records instanceof \Illuminate\Pagination\AbstractPaginator
        ? collect($records->items())
        : collect($records ?? []);

    $printGeneratedCount = $printRecords->count();

    $printTotalRecords = $totalComplaints ?? $printGeneratedCount;

    $printAiAssessed = data_get($urgencySummary ?? [], 'ai_assessed', 0);
    $printVerified = data_get($urgencySummary ?? [], 'verified', 0);
    $printPending = data_get($urgencySummary ?? [], 'pending_verification', 0);
    $printChanged = data_get($urgencySummary ?? [], 'urgency_changed', 0);

    $printVerificationRate = (float) ($urgencyVerificationRate ?? 0);

    $printTrendRows = collect($trend ?? []);
    $printStatusRows = collect($statusStats ?? []);
    $printDivisionRows = collect($divisionStats ?? []);
    $printCategoryRows = collect($categoryStats ?? []);
    $printAiUrgencyRows = collect($urgencyStats ?? []);
    $printVerifiedUrgencyRows = collect($verifiedUrgencyStats ?? []);
    $printFeedbackRows = collect($feedbackStats ?? []);

    $printAverageRating = $averageRating ?? null;

    $printActive = $activeComplaints
        ?? $activeCount
        ?? null;

    $printCompleted = $completedComplaints
        ?? $completedCount
        ?? null;

    $printResolutionRate = $resolutionRate ?? 0;
@endphp

{{-- ================================================================
     REPORT FILTERS
================================================================ --}}

<section class="report-section avoid-break">

    <h3 class="report-section-title">Report Coverage and Filters</h3>

    <div class="report-grid">

        <div class="report-field">
            <div class="report-field-label">Reporting Period</div>
            <div class="report-field-value">
                {{ ucfirst($period ?? 'Custom / All Records') }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Division</div>
            <div class="report-field-value">
                {{ $selectedDivisionName ?? $divisionName ?? 'All Divisions' }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Date From</div>
            <div class="report-field-value">
                {{ !empty($dateFrom) ? \Carbon\Carbon::parse($dateFrom)->format('F d, Y') : 'No start date' }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Date To</div>
            <div class="report-field-value">
                {{ !empty($dateTo) ? \Carbon\Carbon::parse($dateTo)->format('F d, Y') : 'No end date' }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Complaint Status</div>
            <div class="report-field-value">
                {{ $selectedStatus ?? request('status') ?? 'All Statuses' }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Trend Interval</div>
            <div class="report-field-value">
                {{ ucfirst($trendInterval ?? request('trend_interval', 'monthly')) }}
            </div>
        </div>

    </div>

</section>

{{-- ================================================================
     OVERVIEW
================================================================ --}}

<section class="report-section">

    <h3 class="report-section-title">Complaint Overview</h3>

    <div class="report-grid">

        <div class="report-field">
            <div class="report-field-label">Total Complaints</div>
            <div class="report-field-value">
                {{ $printNumber($printTotalRecords) }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Active Complaints</div>
            <div class="report-field-value">
                {{ $printActive !== null ? $printNumber($printActive) : 'See Status Breakdown' }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Completed / Closed</div>
            <div class="report-field-value">
                {{ $printCompleted !== null ? $printNumber($printCompleted) : 'See Status Breakdown' }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Resolution Rate</div>
            <div class="report-field-value">
                {{ $printPercent($printResolutionRate) }}
            </div>
        </div>

    </div>

</section>

{{-- ================================================================
     AI URGENCY VERIFICATION
================================================================ --}}

<section class="report-section">

    <h3 class="report-section-title">AI Urgency Assessment and Verification</h3>

    <div class="report-grid">

        <div class="report-field">
            <div class="report-field-label">AI-Assessed Complaints</div>
            <div class="report-field-value">
                {{ $printNumber($printAiAssessed) }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Verified Urgency</div>
            <div class="report-field-value">
                {{ $printNumber($printVerified) }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Pending Verification</div>
            <div class="report-field-value">
                {{ $printNumber($printPending) }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">AI Urgency Changed After Verification</div>
            <div class="report-field-value">
                {{ $printNumber($printChanged) }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Urgency Verification Rate</div>
            <div class="report-field-value">
                {{ $printPercent($printVerificationRate) }}
            </div>
        </div>

    </div>

    <div class="report-text-block" style="margin-top: 3mm;">
        AI-assessed urgency is the urgency level generated by the complaint
        analysis system. Verified urgency is the level confirmed or adjusted
        by authorized Customer Service personnel. A changed urgency means
        the verified level differs from the original AI assessment.
    </div>

</section>

{{-- ================================================================
     COMPLAINT TREND
================================================================ --}}

<section class="report-section">

    <h3 class="report-section-title">Complaint Volume Trend</h3>

    <div class="report-table-wrapper">

        <table class="report-table">

            <thead>
                <tr>
                    <th style="width: 55%;">Period</th>
                    <th style="width: 45%;" class="text-right">Complaints</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($printTrendRows as $row)
                    <tr>
                        <td>
                            {{ data_get($row, 'label', data_get($row, 'period', '—')) }}
                        </td>
                        <td class="text-right">
                            {{ $printNumber(data_get($row, 'total', data_get($row, 'count', data_get($row, 'complaints_count', 0)))) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">
                            No trend data available for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</section>

{{-- ================================================================
     STATUS BREAKDOWN
================================================================ --}}

<section class="report-section">

    <h3 class="report-section-title">Complaint Status Breakdown</h3>

    <div class="report-table-wrapper">

        <table class="report-table">

            <thead>
                <tr>
                    <th style="width: 55%;">Status</th>
                    <th style="width: 45%;" class="text-right">Complaints</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($printStatusRows as $row)
                    <tr>
                        <td>
                            {{ data_get($row, 'status', data_get($row, 'name', 'Unknown')) }}
                        </td>
                        <td class="text-right">
                            {{ $printNumber(data_get($row, 'total', data_get($row, 'count', data_get($row, 'complaints_count', 0)))) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">
                            No status data available.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</section>

{{-- ================================================================
     DIVISION BREAKDOWN
================================================================ --}}

<section class="report-section">

    <h3 class="report-section-title">Complaints by Division</h3>

    <div class="report-table-wrapper">

        <table class="report-table">

            <thead>
                <tr>
                    <th style="width: 65%;">Division</th>
                    <th style="width: 35%;" class="text-right">Complaints</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($printDivisionRows as $row)
                    <tr>
                        <td>
                            {{ data_get($row, 'division_name', data_get($row, 'name', 'Unassigned')) }}
                        </td>
                        <td class="text-right">
                            {{ $printNumber(data_get($row, 'total', data_get($row, 'count', data_get($row, 'complaints_count', 0)))) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">
                            No division data available.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</section>

{{-- ================================================================
     CATEGORY BREAKDOWN
================================================================ --}}

<section class="report-section">

    <h3 class="report-section-title">Complaints by Category</h3>

    <div class="report-table-wrapper">

        <table class="report-table">

            <thead>
                <tr>
                    <th style="width: 65%;">Category</th>
                    <th style="width: 35%;" class="text-right">Complaints</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($printCategoryRows as $row)
                    <tr>
                        <td>
                            {{ data_get($row, 'category_name', data_get($row, 'name', 'Uncategorized')) }}
                        </td>
                        <td class="text-right">
                            {{ $printNumber(data_get($row, 'total', data_get($row, 'count', data_get($row, 'complaints_count', 0)))) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">
                            No category data available.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</section>

{{-- ================================================================
     AI URGENCY DISTRIBUTION
================================================================ --}}

<section class="report-section">

    <h3 class="report-section-title">AI-Suggested Urgency Distribution</h3>

    <div class="report-table-wrapper">

        <table class="report-table">

            <thead>
                <tr>
                    <th style="width: 55%;">AI Urgency Level</th>
                    <th style="width: 25%;" class="text-right">Complaints</th>
                    <th style="width: 20%;" class="text-right">Percentage</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($printAiUrgencyRows as $row)
                    <tr>
                        <td>
                            {{ data_get($row, 'urgency_level', data_get($row, 'name', 'Not Assessed')) }}
                        </td>
                        <td class="text-right">
                            {{ $printNumber(data_get($row, 'total', data_get($row, 'count', 0))) }}
                        </td>
                        <td class="text-right">
                            {{ $printPercent(data_get($row, 'percentage', 0)) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">
                            No AI urgency data available.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</section>

{{-- ================================================================
     VERIFIED URGENCY DISTRIBUTION
================================================================ --}}

<section class="report-section">

    <h3 class="report-section-title">Customer Service Verified Urgency Distribution</h3>

    <div class="report-table-wrapper">

        <table class="report-table">

            <thead>
                <tr>
                    <th style="width: 55%;">Verification Category</th>
                    <th style="width: 25%;" class="text-right">Complaints</th>
                    <th style="width: 20%;" class="text-right">Percentage</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($printVerifiedUrgencyRows as $row)
                    <tr>
                        <td>
                            {{ data_get($row, 'verified_urgency_level', data_get($row, 'name', 'Pending Verification')) }}
                        </td>
                        <td class="text-right">
                            {{ $printNumber(data_get($row, 'total', data_get($row, 'count', 0))) }}
                        </td>
                        <td class="text-right">
                            {{ $printPercent(data_get($row, 'percentage', 0)) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">
                            No verified urgency data available.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</section>

{{-- ================================================================
     CONSUMER FEEDBACK
================================================================ --}}

<section class="report-section">

    <h3 class="report-section-title">Consumer Feedback Summary</h3>

    <div class="report-grid">

        <div class="report-field">
            <div class="report-field-label">Average Rating</div>
            <div class="report-field-value">
                {{ $printAverageRating !== null ? number_format((float) $printAverageRating, 2) . ' / 5' : 'No rating data' }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Feedback Records</div>
            <div class="report-field-value">
                {{ $printNumber($feedbackCount ?? $feedbackTotal ?? 0) }}
            </div>
        </div>

    </div>

    <div class="report-table-wrapper" style="margin-top: 3mm;">

        <table class="report-table">

            <thead>
                <tr>
                    <th style="width: 55%;">Rating</th>
                    <th style="width: 45%;" class="text-right">Count</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($printFeedbackRows as $row)
                    <tr>
                        <td>
                            {{ data_get($row, 'rating', data_get($row, 'name', 'Unrated')) }}
                        </td>
                        <td class="text-right">
                            {{ $printNumber(data_get($row, 'total', data_get($row, 'count', 0))) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">
                            No consumer feedback records available.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</section>

{{-- ================================================================
     DETAILED COMPLAINT REGISTER
================================================================ --}}

<section class="report-section">

    <div class="report-section-heading-row">

        <h3 class="report-section-title">
            Detailed Complaint Register
        </h3>

        <div class="report-record-count">
            Records included: <strong>{{ $printNumber($printGeneratedCount) }}</strong>
        </div>

    </div>

    <div class="report-table-wrapper">

        <table class="report-table">

            <thead>
                <tr>
                    <th style="width: 10%;">Complaint No.</th>
                    <th style="width: 14%;">Date</th>
                    <th style="width: 17%;">Category</th>
                    <th style="width: 15%;">Division</th>
                    <th style="width: 11%;">Status</th>
                    <th style="width: 10%;">AI Urgency</th>
                    <th style="width: 11%;">Verified Urgency</th>
                    <th style="width: 12%;">Verification Status</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($printRecords as $complaint)

                    @php
                        $analysis = $complaint->aiAnalysis ?? null;

                        $categoryName = $complaint->category?->name
                            ?? $complaint->category_name
                            ?? 'Uncategorized';

                        $divisionName = $complaint->division?->name
                            ?? $complaint->division_name
                            ?? 'Unassigned';

                        $complaintNumber = $complaint->complaint_number
                            ?? ('CMP-' . str_pad((string) $complaint->id, 6, '0', STR_PAD_LEFT));

                        $complaintDate = $complaint->created_at
                            ? $complaint->created_at->format('M d, Y h:i A')
                            : '—';
                    @endphp

                    <tr>

                        <td>{{ $complaintNumber }}</td>

                        <td>{{ $complaintDate }}</td>

                        <td>{{ $categoryName }}</td>

                        <td>{{ $divisionName }}</td>

                        <td>{{ $complaint->status ?? '—' }}</td>

                        <td>{{ $printAiUrgency($analysis) }}</td>

                        <td>{{ $printUrgency($analysis) }}</td>

                        <td>{{ $printVerificationStatus($analysis) }}</td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center">
                            No complaints match the selected report filters.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <p style="margin-top: 2mm; color: #6b7280; font-size: 7pt;">
        The detailed register contains {{ $printNumber($printGeneratedCount) }}
        complaint record(s) included in this print operation. If the report
        is paginated, ensure the print controller supplies the complete
        filtered collection.
    </p>

</section>

{{-- ================================================================
     DEFINITIONS
================================================================ --}}

<section class="report-section">

    <h3 class="report-section-title">Report Definitions</h3>

    <div class="report-text-block">
        Total Complaints: Complaints included in the selected report filters.

        AI-Suggested Urgency: The urgency level recorded by the AI complaint analysis.

        Verified Urgency: The urgency level confirmed or adjusted by Customer Service.

        Pending Verification: A complaint analysis that has not yet received a verified urgency value.

        Urgency Changed: A verified urgency level that differs from the AI-suggested level.

        Verification Rate: The percentage calculated by the report controller. Confirm that its numerator and denominator use the same eligible set of AI-assessed complaints.
    </div>

</section>

@endsection
