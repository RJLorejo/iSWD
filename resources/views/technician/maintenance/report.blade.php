@extends('technician.layouts.app')

@section('title', 'Service Accomplishment Report')

@section('content')

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-6">

            <a href="{{ route('technician.complaints.show', $complaint) }}"
                class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 mb-4">

                <i class="fas fa-arrow-left"></i>
                Back to Complaint

            </a>

            <h1 class="text-2xl font-bold text-gray-900">
                Service Accomplishment Report
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Record the findings and materials or parts involved in the completed maintenance.
            </p>

        </div>


        @if (session('success'))
            <div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 text-sm text-green-700">

                <i class="fas fa-circle-check mr-2"></i>
                {{ session('success') }}

            </div>
        @endif


        @if (session('error'))
            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">

                <i class="fas fa-circle-exclamation mr-2"></i>
                {{ session('error') }}

            </div>
        @endif


        @if ($errors->any())

            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4">

                <p class="font-semibold text-red-800 mb-2">
                    Please correct the following:
                </p>

                <ul class="list-disc ml-5 text-sm text-red-700 space-y-1">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif


        @if ($complaint->maintenanceReport?->review_status === 'Returned')

            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">

                <div class="flex gap-3">

                    <i class="fas fa-rotate-left text-amber-600 mt-1"></i>

                    <div>

                        <p class="font-semibold text-amber-900">
                            Report Returned for Correction
                        </p>

                        <p class="text-sm text-amber-700 mt-1">
                            Review the manager's remarks, make the necessary corrections,
                            and resubmit the accomplishment report.
                        </p>

                        @if ($complaint->maintenanceReport->review_remarks)
                            <div class="mt-3 p-3 rounded-xl bg-white border border-amber-200 text-sm text-gray-700">
                                {{ $complaint->maintenanceReport->review_remarks }}
                            </div>
                        @endif

                    </div>

                </div>

            </div>

        @endif


        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 sm:p-6 mb-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div>

                    <div class="flex flex-wrap items-center gap-2">

                        <span class="text-lg font-bold text-gray-900">
                            {{ $complaint->complaint_no }}
                        </span>

                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                            {{ $complaint->status === 'Completed' ? 'Accomplished' : $complaint->status }}
                        </span>

                    </div>

                    <p class="text-sm font-medium text-gray-800 mt-2">
                        {{ $complaint->type?->name ?? ($complaint->category?->name ?? 'Water Service Concern') }}
                    </p>

                    <p class="text-sm text-gray-500 mt-1">
                        {{ $complaint->address ?: 'No location recorded.' }}
                    </p>

                </div>

                <div class="sm:text-right">

                    <p class="text-xs text-gray-500">
                        Maintenance Team
                    </p>

                    <div class="mt-2 flex flex-wrap gap-2 sm:justify-end">

                        @foreach ($complaint->technicians as $technician)
                            <span
                                class="inline-flex items-center gap-1.5
                       px-2.5 py-1.5 rounded-lg
                       bg-sky-50 text-sky-700
                       border border-sky-100
                       text-xs font-semibold">
                                <i class="fas fa-user-gear"></i>

                                {{ $technician->full_name }}

                                @if ($technician->id === auth()->id())
                                    <span class="text-sky-500">
                                        (You)
                                    </span>
                                @endif
                            </span>
                        @endforeach

                    </div>

                </div>

            </div>

        </div>


        <form method="POST" action="{{ route('technician.maintenance-reports.store', $complaint) }}"
            enctype="multipart/form-data">

            @csrf


            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 sm:p-6 mb-6">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-gray-900">
                        Inspection Findings
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Record the condition found during inspection and its identified cause.
                    </p>

                </div>


                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Diagnosis / Findings
                            <span class="text-red-500">*</span>
                        </label>

                        <textarea name="diagnosis" rows="6" required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Describe the actual problem or condition found during inspection...">{{ old('diagnosis', $complaint->maintenanceReport?->diagnosis) }}</textarea>

                        @error('diagnosis')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Root Cause
                            <span class="text-red-500">*</span>
                        </label>

                        <textarea name="root_cause" rows="6" required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Describe the identified cause of the problem...">{{ old('root_cause', $complaint->maintenanceReport?->root_cause) }}</textarea>

                        @error('root_cause')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </div>


            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 sm:p-6 mb-6">

                <div class="mb-5">

                    <h2 class="text-lg font-bold text-gray-900">
                        Materials / Parts
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Record materials or parts that were removed, replaced, or installed.
                    </p>

                </div>


                <textarea name="materials_parts" rows="6"
                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Example: Removed damaged coupling, replaced 1/2-inch PVC coupling, installed 2 meters of PVC pipe...">{{ old('materials_parts', $complaint->maintenanceReport?->materials_parts) }}</textarea>

                @error('materials_parts')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 sm:p-6 mb-6">

                <div class="mb-5">

                    <h2 class="text-lg font-bold text-gray-900">
                        Plumber Notes
                        <span class="text-sm font-normal text-gray-400">
                            (Optional)
                        </span>
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Add any important observation that is not already covered above.
                    </p>

                </div>


                <textarea name="technician_notes" rows="5"
                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Additional observations, if any...">{{ old('technician_notes', $complaint->maintenanceReport?->technician_notes) }}</textarea>

                @error('technician_notes')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 sm:p-6 mb-6">

                <div class="mb-5">

                    <h2 class="text-lg font-bold text-gray-900">
                        Maintenance Photos
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Add before and after photos when available.
                    </p>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Before Maintenance
                        </label>

                        <input type="file" name="before_photo" accept="image/*"
                            class="block w-full text-sm text-gray-600 border border-gray-300 rounded-xl p-3">

                        @error('before_photo')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror


                        @if ($complaint->maintenanceReport?->before_photo)
                            <div class="mt-4">

                                <p class="text-xs font-medium text-gray-500 mb-2">
                                    Current Photo
                                </p>

                                <img src="{{ asset('storage/' . $complaint->maintenanceReport->before_photo) }}"
                                    alt="Before maintenance"
                                    class="w-full max-h-72 object-cover rounded-xl border border-gray-200">

                            </div>
                        @endif

                    </div>


                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            After Maintenance
                        </label>

                        <input type="file" name="after_photo" accept="image/*"
                            class="block w-full text-sm text-gray-600 border border-gray-300 rounded-xl p-3">

                        @error('after_photo')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror


                        @if ($complaint->maintenanceReport?->after_photo)
                            <div class="mt-4">

                                <p class="text-xs font-medium text-gray-500 mb-2">
                                    Current Photo
                                </p>

                                <img src="{{ asset('storage/' . $complaint->maintenanceReport->after_photo) }}"
                                    alt="After maintenance"
                                    class="w-full max-h-72 object-cover rounded-xl border border-gray-200">

                            </div>
                        @endif

                    </div>

                </div>

            </div>


            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                <a href="{{ route('technician.complaints.show', $complaint) }}"
                    class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">

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
