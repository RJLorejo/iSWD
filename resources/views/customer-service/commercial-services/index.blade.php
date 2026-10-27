@extends('customer-service.layouts.app')

@section('title', 'Commercial Services Complaints')

@section('content')
    <div class="mx-auto max-w-7xl min-w-0 space-y-5 px-3 py-5 sm:px-6">

        {{-- Page Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-wider text-sky-700">
                    Customer Service / Commercial Services
                </p>

                <h1 class="mt-1 text-2xl font-bold text-slate-900">
                    Commercial Services Complaints
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Verified commercial complaints, including active processing and historical outcomes.
                </p>
            </div>
        </div>

        {{-- Summary Statistics --}}
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ([['label' => 'Matching Complaints', 'count' => $counts->sum(), 'icon' => 'fa-folder-open'], ['label' => 'Verified', 'count' => $counts->get('Verified', 0), 'icon' => 'fa-circle-check'], ['label' => 'CS Processing', 'count' => $counts->get('CS Processing', 0), 'icon' => 'fa-gear'], ['label' => 'Closed', 'count' => $counts->get('Closed', 0), 'icon' => 'fa-lock']] as $stat)
                <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-medium text-slate-500">
                        <i class="fas {{ $stat['icon'] }} mr-1 text-sky-600" aria-hidden="true"></i>
                        {{ $stat['label'] }}
                    </p>

                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        {{ number_format($stat['count']) }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Detect Active Filters --}}
        @php
            $hasCommercialFilters =
                request()->filled('search') ||
                (request('status', 'all') !== 'all' && request('status') !== '') ||
                request()->filled('date_from') ||
                request()->filled('date_to');
        @endphp

        {{-- Search and Filters --}}
        <form method="GET" action="{{ route('customer-service.commercial-services.index') }}"
            class="w-full rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5 lg:p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-[2fr_1fr_1fr_1fr] xl:items-end">

                {{-- Search Complaint Number, Account, Consumer, or Address --}}
                <div class="min-w-0">
                    <label for="commercial-search" class="mb-1.5 block text-xs font-semibold text-slate-600">
                        Search Complaint No. / Account / Name / Address
                    </label>

                    <input id="commercial-search" name="search" type="search" value="{{ request('search') }}"
                        placeholder="Enter complaint no., account, name, or address..."
                        class="block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm transition focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                </div>

                {{-- Status Filter --}}
                <div class="min-w-0">
                    <label for="commercial-status" class="mb-1.5 block text-xs font-semibold text-slate-600">
                        Status
                    </label>

                    <select id="commercial-status" name="status"
                        class="block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm transition focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                        @foreach ([
            'all' => 'All status',
            'Verified' => 'Verified',
            'CS Processing' => 'CS Processing',
            'For Maintenance' => 'For Maintenance',
            'Assigned' => 'Assigned',
            'In Progress' => 'In Progress',
            'Completed' => 'Completed',
            'Closed' => 'Closed',
        ] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status', 'all') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Verified From --}}
                <div class="min-w-0">
                    <label for="commercial-from" class="mb-1.5 block text-xs font-semibold text-slate-600">
                        From
                    </label>

                    <input id="commercial-from" name="date_from" type="date" value="{{ request('date_from') }}"
                        class="block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm transition focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                </div>

                {{-- Verified To --}}
                <div class="min-w-0">
                    <label for="commercial-to" class="mb-1.5 block text-xs font-semibold text-slate-600">
                        To
                    </label>

                    <input id="commercial-to" name="date_to" type="date" value="{{ request('date_to') }}"
                        class="block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm transition focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                </div>
            </div>

            {{-- Validation Message --}}
            @error('date_to')
                <p class="mt-2 text-xs font-medium text-red-600">
                    {{ $message }}
                </p>
            @enderror

            @error('date_from')
                <p class="mt-2 text-xs font-medium text-red-600">
                    {{ $message }}
                </p>
            @enderror

            @error('search')
                <p class="mt-2 text-xs font-medium text-red-600">
                    {{ $message }}
                </p>
            @enderror

            {{-- Filter Actions --}}
            <div class="mt-5 flex flex-wrap items-center justify-start gap-2 border-t border-slate-100 pt-4">

                <button type="submit"
                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                    <i class="fas fa-filter" aria-hidden="true"></i>
                    <span>Filter</span>
                </button>

                @if ($hasCommercialFilters)
                    <a href="{{ route('customer-service.commercial-services.index') }}" aria-label="Reset all filters"
                        title="Reset all filters"
                        class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-slate-300">
                        <i class="fas fa-rotate-left" aria-hidden="true"></i>
                        <span class="sr-only">Reset filters</span>
                    </a>
                @endif

                <a href="{{ route('customer-service.commercial-services.print', request()->query()) }}" target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-sky-200 hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                    <i class="fas fa-print text-sky-600" aria-hidden="true"></i>
                    <span>Print Report</span>
                </a>
            </div>
        </form>

        {{-- Complaint Records --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-4 sm:px-5">
                <h2 class="font-semibold text-slate-900">
                    Verified Commercial Records
                </h2>

                <span class="text-xs text-slate-500">
                    {{ $complaints->total() }} matching records
                </span>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full min-w-[950px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-600">
                        <tr>
                            <th class="px-5 py-3">Complaint No.</th>
                            <th class="px-5 py-3">Complainant / Address</th>
                            <th class="px-5 py-3">Complaint Type</th>
                            <th class="px-5 py-3">Verified On</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($complaints as $complaint)
                            @php
                                $rawAddress = $complaint->consumer?->address;

                                // Support an Eloquent address relationship, an array, or JSON.
                                if ($rawAddress instanceof \Illuminate\Database\Eloquent\Model) {
                                    $addressData = $rawAddress->getAttributes();
                                } elseif (is_array($rawAddress)) {
                                    $addressData = $rawAddress;
                                } elseif (is_object($rawAddress)) {
                                    $addressData = (array) $rawAddress;
                                } elseif (is_string($rawAddress)) {
                                    $decodedAddress = json_decode($rawAddress, true);

                                    $addressData = is_array($decodedAddress) ? $decodedAddress : null;
                                } else {
                                    $addressData = null;
                                }

                                if (is_array($addressData)) {
                                    $addressParts = [
                                        $addressData['house_no'] ?? null,
                                        $addressData['street'] ?? null,
                                        !empty($addressData['purok']) ? 'Purok ' . $addressData['purok'] : null,
                                        !empty($addressData['barangay'])
                                            ? 'Barangay ' . $addressData['barangay']
                                            : null,
                                        $addressData['municipality'] ?? null,
                                        $addressData['province'] ?? null,
                                    ];

                                    $displayAddress = implode(
                                        ', ',
                                        array_filter(
                                            $addressParts,
                                            fn($part) => is_string($part) || is_numeric($part)
                                                ? trim((string) $part) !== ''
                                                : false,
                                        ),
                                    );
                                } else {
                                    $displayAddress = is_string($rawAddress) ? trim($rawAddress) : '';
                                }
                            @endphp


                            <tr class="transition hover:bg-slate-50">

                                {{-- Complaint Number --}}
                                <td class="whitespace-nowrap px-5 py-4 font-bold text-sky-700">
                                    {{ $complaint->complaint_no }}
                                </td>

                                {{-- Consumer Information and Address --}}
                                <td class="px-5 py-4">
                                    <p class="font-medium text-slate-900">
                                        {{ $complaint->consumer?->full_name ?? $complaint->complainant_name }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Account: {{ $complaint->consumer?->account_number ?? 'Walk-in' }}
                                    </p>

                                    <p class="mt-1 max-w-xs break-words text-xs leading-relaxed text-slate-500">
                                        <i class="fas fa-location-dot mr-1 text-sky-600" aria-hidden="true"></i>
                                        {{ $displayAddress !== '' ? $displayAddress : 'Address unavailable' }}
                                    </p>
                                </td>

                                {{-- Complaint Category --}}
                                <td class="px-5 py-4 text-slate-700">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </td>

                                {{-- Verification Date --}}
                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                    {{ $complaint->verified_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? '—' }}
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                        {{ $complaint->status }}
                                    </span>
                                </td>

                                {{-- Action --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a href="{{ route('customer-service.complaints.show', [
                                        'complaint' => $complaint,
                                        'return_to' => 'commercial-services',
                                    ]) }}"
                                        class="font-semibold text-sky-700 hover:underline">
                                        View details
                                        <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                                    No verified Commercial Services complaints match your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Complaint Cards --}}
            <div class="divide-y divide-slate-100 lg:hidden">
                @forelse ($complaints as $complaint)
                    @php
                        $rawAddress = $complaint->consumer?->address;

                        // Support an Eloquent address relationship, an array, or JSON.
                        if ($rawAddress instanceof \Illuminate\Database\Eloquent\Model) {
                            $addressData = $rawAddress->getAttributes();
                        } elseif (is_array($rawAddress)) {
                            $addressData = $rawAddress;
                        } elseif (is_object($rawAddress)) {
                            $addressData = (array) $rawAddress;
                        } elseif (is_string($rawAddress)) {
                            $decodedAddress = json_decode($rawAddress, true);

                            $addressData = is_array($decodedAddress) ? $decodedAddress : null;
                        } else {
                            $addressData = null;
                        }

                        if (is_array($addressData)) {
                            $addressParts = [
                                $addressData['house_no'] ?? null,
                                $addressData['street'] ?? null,
                                !empty($addressData['purok']) ? 'Purok ' . $addressData['purok'] : null,
                                !empty($addressData['barangay']) ? 'Barangay ' . $addressData['barangay'] : null,
                                $addressData['municipality'] ?? null,
                                $addressData['province'] ?? null,
                            ];

                            $displayAddress = implode(
                                ', ',
                                array_filter(
                                    $addressParts,
                                    fn($part) => is_string($part) || is_numeric($part)
                                        ? trim((string) $part) !== ''
                                        : false,
                                ),
                            );
                        } else {
                            $displayAddress = is_string($rawAddress) ? trim($rawAddress) : '';
                        }
                    @endphp


                    <div class="space-y-3 p-4">

                        {{-- Complaint Number and Status --}}
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <strong class="break-words text-sm text-sky-700">
                                {{ $complaint->complaint_no }}
                            </strong>

                            <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                {{ $complaint->status }}
                            </span>
                        </div>

                        {{-- Complaint and Consumer Details --}}
                        <div>
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $complaint->category?->name ?? 'Uncategorized' }}
                            </p>

                            <p class="mt-1 break-words text-sm text-slate-600">
                                {{ $complaint->consumer?->full_name ?? $complaint->complainant_name }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Account: {{ $complaint->consumer?->account_number ?? 'Walk-in' }}
                            </p>

                            <p class="mt-1 break-words text-xs leading-relaxed text-slate-500">
                                <i class="fas fa-location-dot mr-1 text-sky-600" aria-hidden="true"></i>
                                {{ $displayAddress !== '' ? $displayAddress : 'Address unavailable' }}
                            </p>
                        </div>
                        {{-- Verification Date --}}
                        <p class="text-xs text-slate-500">
                            Verified
                            {{ $complaint->verified_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? '—' }}
                        </p>

                        {{-- View Complaint --}}
                        <a href="{{ route('customer-service.complaints.show', [
                            'complaint' => $complaint,
                            'return_to' => 'commercial-services',
                        ]) }}"
                            class="inline-flex items-center gap-2 text-sm font-bold text-sky-700 hover:underline">
                            View complaint
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                @empty
                    <p class="p-8 text-center text-sm text-slate-500">
                        No verified Commercial Services complaints match your filters.
                    </p>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($complaints->hasPages())
                <div class="border-t border-slate-100 px-4 py-4">
                    {{ $complaints->links() }}
                </div>
            @endif

        </section>
    </div>
@endsection
