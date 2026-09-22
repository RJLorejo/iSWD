@extends('maintenance-manager.layouts.app')

@section('title', 'Maintenance Reviews')

@section('content')

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Maintenance Review
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Review and validate technician maintenance reports.
                </p>
            </div>

        </div>


        {{-- SUMMARY --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            <div class="bg-white border rounded-2xl p-6 shadow-sm">

                <p class="text-sm text-slate-500">
                    Pending Review
                </p>

                <p class="text-3xl font-bold text-amber-600 mt-2">
                    {{ $pendingCount }}
                </p>

            </div>


            <div class="bg-white border rounded-2xl p-6 shadow-sm">

                <p class="text-sm text-slate-500">
                    Returned
                </p>

                <p class="text-3xl font-bold text-red-600 mt-2">
                    {{ $returnedCount }}
                </p>

            </div>


            <div class="bg-white border rounded-2xl p-6 shadow-sm">

                <p class="text-sm text-slate-500">
                    Approved
                </p>

                <p class="text-3xl font-bold text-green-600 mt-2">
                    {{ $approvedCount }}
                </p>

            </div>

        </div>


        {{-- FILTERS --}}
        <div class="bg-white border rounded-2xl shadow-sm p-5">

            <form method="GET" action="{{ route('maintenance-manager.maintenance-reviews.index') }}"
                class="grid md:grid-cols-4 gap-4">

                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Search
                    </label>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Complaint no. or subject" class="w-full rounded-xl border-slate-300">

                </div>


                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Status
                    </label>

                    <select name="status" class="w-full rounded-xl border-slate-300">

                        <option value="Pending Review" @selected(request('status', 'Pending Review') === 'Pending Review')>
                            Pending Review
                        </option>

                        <option value="Returned" @selected(request('status') === 'Returned')>
                            Returned
                        </option>

                        <option value="Approved" @selected(request('status') === 'Approved')>
                            Approved
                        </option>

                        <option value="All" @selected(request('status') === 'All')>
                            All
                        </option>

                    </select>

                </div>


                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        From
                    </label>

                    <input type="date" name="from" value="{{ request('from') }}"
                        class="w-full rounded-xl border-slate-300">

                </div>


                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        To
                    </label>

                    <input type="date" name="to" value="{{ request('to') }}"
                        class="w-full rounded-xl border-slate-300">

                </div>


                <div class="md:col-span-4 flex justify-end">

                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-sky-700 text-white font-medium hover:bg-sky-800">
                        <i class="fas fa-filter mr-2"></i>
                        Apply Filters
                    </button>

                </div>

            </form>

        </div>


        {{-- TABLE --}}
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50 border-b">

                        <tr>

                            <th class="text-left px-6 py-4 text-sm font-semibold">
                                Complaint
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold">
                                Plumber
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold">
                                Complaint Type
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold">
                                Submitted
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold">
                                Status
                            </th>

                            <th class="text-right px-6 py-4 text-sm font-semibold">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y">

                        @forelse($reports as $report)
                            <tr class="hover:bg-slate-50">

                                <td class="px-6 py-4">

                                    <div class="font-semibold text-slate-800">
                                        {{ $report->complaint?->complaint_no }}
                                    </div>

                                </td>


                                <td class="px-6 py-4">

                                    {{ $report->technician?->first_name }}
                                    {{ $report->technicians?->last_name }}

                                </td>


                                <td class="px-6 py-4 text-sm">

                                    {{ $report->complaint?->complaint_type?->name ?? '—' }}

                                </td>


                                <td class="px-6 py-4 text-sm text-slate-500">

                                    {{ $report->submitted_at ? $report->submitted_at->format('M d, Y h:i A') : 'Not yet submitted' }}

                                </td>


                                <td class="px-6 py-4">

                                    @if ($report->review_status === 'Pending Review')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                            Pending Review
                                        </span>
                                    @elseif($report->review_status === 'Returned')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Returned
                                        </span>
                                    @elseif($report->review_status === 'Approved')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            Approved
                                        </span>
                                    @endif

                                </td>


                                <td class="px-6 py-4 text-right">

                                    <a href="{{ route('maintenance-manager.maintenance-reviews.show', $report) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-700 text-white text-sm font-medium hover:bg-sky-800">

                                        <i class="fas fa-eye"></i>

                                        Review

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">

                                    <i class="fas fa-clipboard-check text-3xl mb-3"></i>

                                    <p>
                                        No maintenance reports found.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="p-5 border-t">

                {{ $reports->links() }}

            </div>

        </div>

    </div>

@endsection
