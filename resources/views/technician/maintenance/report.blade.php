@extends('technician.layouts.app')

@section('content')

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER --}}
        <div class="mb-8">

            <div class="flex items-center gap-3 mb-2">

                <a href="{{ route('technician.complaints.show', $complaint) }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>

                <h1 class="text-2xl font-bold text-gray-900">
                    Service Accomplishment Report
                </h1>

            </div>

            <p class="text-gray-500">
                Document the service work performed for
                <span class="font-semibold text-gray-700">
                    {{ $complaint->complaint_no }}
                </span>
            </p>

        </div>


        {{-- ALERTS --}}

        @if (session('success'))
            <div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 text-green-700">
                <i class="fas fa-circle-check mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-red-700">
                <i class="fas fa-circle-exclamation mr-2"></i>
                {{ session('error') }}
            </div>
        @endif


        {{-- VALIDATION ERRORS --}}

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4">

                <p class="font-semibold text-red-800 mb-2">
                    Please correct the following:
                </p>

                <ul class="list-disc ml-5 text-sm text-red-700">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>
        @endif


        {{-- COMPLAINT SUMMARY --}}

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                <div>

                    <div class="flex items-center gap-3 mb-2">

                        <span class="font-bold text-gray-900">
                            {{ $complaint->complaint_no }}
                        </span>

                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                            {{ $complaint->status }}
                        </span>

                    </div>

                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ $complaint->subject }}
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        {{ $complaint->address }}
                    </p>

                </div>

                <div class="text-sm text-gray-500">

                    <div class="mt-1">
                        Complaint Type: <span class="font-semibold text-gray-700">
                            {{ $complaint->type?->name ?? 'N/A' }}
                        </span>
                    </div>

                </div>

            </div>

        </div>

        {{-- ASSIGNED MAINTENANCE TEAM --}}

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">

            <div class="flex items-center justify-between mb-5">

                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        Assigned Maintenance Team
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Plumber assigned to this complaint.
                    </p>
                </div>

                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>

            </div>

            @if ($complaint->technicians->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    @foreach ($complaint->technicians as $technician)
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-gray-50 border border-gray-200">

                            <img src="{{ $technician->avatar_url }}" alt="{{ $technician->full_name }}"
                                class="w-11 h-11 rounded-full object-cover border border-gray-200">

                            <div class="min-w-0">

                                <p class="font-semibold text-gray-900 truncate">
                                    {{ $technician->full_name }}

                                    @if ($technician->id === auth()->id())
                                        <span
                                            class="ml-1 inline-flex px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs">
                                            You
                                        </span>
                                    @endif
                                </p>

                                <p class="text-xs text-gray-500">
                                    Plumber
                                </p>

                            </div>

                        </div>
                    @endforeach

                </div>
            @else
                <p class="text-sm text-gray-500">
                    No maintenance plumbers are currently assigned.
                </p>
            @endif

        </div>


        {{-- REPORT FORM --}}

        <form method="POST" action="{{ route('technician.maintenance-reports.store', $complaint) }}"
            enctype="multipart/form-data">

            @csrf


            {{-- SECTION 1 --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">

                <h2 class="text-lg font-bold text-gray-900 mb-1">
                    1. Inspection Findings & Root Cause
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    Document the actual condition observed during inspection and the identified cause.
                </p>


                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Diagnosis <span class="text-red-500">*</span>
                        </label>

                        <textarea name="diagnosis" rows="5" required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Describe the identified problem or condition...">{{ old('diagnosis', $complaint->maintenanceReport?->diagnosis) }}</textarea>

                    </div>


                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Root Cause <span class="text-red-500">*</span>
                        </label>

                        <textarea name="root_cause" rows="5" required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Describe the cause of the problem...">{{ old('root_cause', $complaint->maintenanceReport?->root_cause) }}</textarea>

                    </div>

                </div>

            </div>



            {{-- SECTION 3 --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">

                <h2 class="text-lg font-bold text-gray-900 mb-1">
                    2. Parts Removed
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    List the resources used during maintenance.
                </p>


                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Parts Removed
                        </label>

                        <textarea name="parts_replaced" rows="5"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g. 2-inch valve, pipe section...">{{ old('parts_replaced', $complaint->maintenanceReport?->parts_replaced) }}</textarea>

                    </div>

                </div>

            </div>


            {{-- SECTION 4 --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">

                <h2 class="text-lg font-bold text-gray-900 mb-1">
                    4. Plumber Notes & Completion
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    Add final observations and completion remarks.
                </p>


                <div class="space-y-6">

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Completion Remarks <span class="text-red-500">*</span>
                        </label>

                        <textarea name="completion_remarks" rows="5" required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Describe the final condition of the maintenance...">{{ old('completion_remarks', $complaint->maintenanceReport?->completion_remarks) }}</textarea>

                    </div>

                </div>

            </div>


            {{-- SECTION 5 --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">

                <h2 class="text-lg font-bold text-gray-900 mb-1">
                    5. Maintenance Photos
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    Upload before and after photos when available.
                </p>


                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Before Maintenance
                        </label>

                        <input type="file" name="before_photo" accept="image/*"
                            class="w-full rounded-xl border border-gray-300 p-3">

                        @if ($complaint->maintenanceReport?->before_photo)
                            <img src="{{ asset('storage/' . $complaint->maintenanceReport->before_photo) }}"
                                class="mt-4 w-full max-h-64 object-cover rounded-xl border">
                        @endif

                    </div>


                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            After Maintenance
                        </label>

                        <input type="file" name="after_photo" accept="image/*"
                            class="w-full rounded-xl border border-gray-300 p-3">

                        @if ($complaint->maintenanceReport?->after_photo)
                            <img src="{{ asset('storage/' . $complaint->maintenanceReport->after_photo) }}"
                                class="mt-4 w-full max-h-64 object-cover rounded-xl border">
                        @endif

                    </div>

                </div>

            </div>


            {{-- SUBMIT --}}

            <div class="flex flex-col sm:flex-row justify-end gap-3">

                <a href="{{ route('technician.complaints.show', $complaint) }}"
                    class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 text-center">
                    Cancel
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-green-600 text-white font-semibold hover:bg-green-700 transition">

                    <i class="fas fa-circle-check"></i>

                    {{ $complaint->maintenanceReport?->review_status === 'Returned'
                        ? 'Resubmit Accomplishment'
                        : 'Submit Accomplishment' }}

                </button>

            </div>

        </form>

    </div>

@endsection
