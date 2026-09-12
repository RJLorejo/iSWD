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
                    Maintenance Reporthsfsdifidsfhisdihi
                </h1>

            </div>

            <p class="text-gray-500">
                Complete the maintenance details for
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

                    <div>
                        Priority:
                        <span class="font-semibold text-gray-700">
                            {{ $complaint->priority }}
                        </span>
                    </div>

                    <div class="mt-1">
                        Category:
                        <span class="font-semibold text-gray-700">
                            {{ $complaint->category?->name ?? 'N/A' }}
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
                        Technicians assigned to this complaint.
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
                                    Maintenance Technician
                                </p>

                            </div>

                        </div>
                    @endforeach

                </div>
            @else
                <p class="text-sm text-gray-500">
                    No maintenance technicians are currently assigned.
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


            {{-- SECTION 2 --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">

                <h2 class="text-lg font-bold text-gray-900 mb-1">
                    2. Maintenance Work Performed
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    Record the actual maintenance work and repair procedure performed on site.
                </p>


                <div class="space-y-6">

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Work Performed <span class="text-red-500">*</span>
                        </label>

                        <textarea name="work_performed" rows="6" required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Describe the actual maintenance work performed...">{{ old('work_performed', $complaint->maintenanceReport?->work_performed) }}</textarea>

                    </div>


                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Repair Procedure <span class="text-red-500">*</span>
                        </label>

                        <textarea name="repair_procedure" rows="6" required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Describe the repair steps or procedure followed...">{{ old('repair_procedure', $complaint->maintenanceReport?->repair_procedure) }}</textarea>

                    </div>

                </div>

            </div>


            {{-- SECTION 3 --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">

                <h2 class="text-lg font-bold text-gray-900 mb-1">
                    3. Materials, Parts & Tools
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    List the resources used during maintenance.
                </p>


                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Materials Used
                        </label>

                        <textarea name="materials_used" rows="5"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g. PVC cement, sealant...">{{ old('materials_used', $complaint->maintenanceReport?->materials_used) }}</textarea>

                    </div>


                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Parts Replaced
                        </label>

                        <textarea name="parts_replaced" rows="5"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g. 2-inch valve, pipe section...">{{ old('parts_replaced', $complaint->maintenanceReport?->parts_replaced) }}</textarea>

                    </div>


                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tools Used
                        </label>

                        <textarea name="tools_used" rows="5"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g. pipe wrench, cutter...">{{ old('tools_used', $complaint->maintenanceReport?->tools_used) }}</textarea>

                    </div>

                </div>

            </div>


            {{-- SECTION 4 --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">

                <h2 class="text-lg font-bold text-gray-900 mb-1">
                    4. Technician Notes & Completion
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    Add final observations and completion remarks.
                </p>


                <div class="space-y-6">

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Technician Notes
                        </label>

                        <textarea name="technician_notes" rows="5"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Additional observations or technical notes...">{{ old('technician_notes', $complaint->maintenanceReport?->technician_notes) }}</textarea>

                    </div>


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
                    class="px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submit Maintenance Report
                </button>

            </div>

        </form>

    </div>

@endsection
