@extends('customer-service.layouts.app')

@section('title', 'Edit Division')

@section('content')

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- HEADER --}}

        <div>

            <a
                href="{{ route('customer-service.divisions.index') }}"
                class="inline-flex items-center gap-2
                       text-sm text-gray-500
                       hover:text-blue-600 mb-3"
            >

                <i class="fas fa-arrow-left"></i>

                Back to Divisions

            </a>

            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                Edit Division
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Update the division information and availability.
            </p>

        </div>


        {{-- ERRORS --}}

        @if ($errors->any())

            <div
                class="rounded-2xl border border-red-200
                       bg-red-50 p-5"
            >

                <div class="flex items-start gap-3">

                    <i
                        class="fas fa-circle-exclamation
                               text-red-600 mt-0.5"
                    ></i>

                    <div>

                        <h2 class="font-semibold text-red-800">
                            Please correct the following:
                        </h2>

                        <ul
                            class="mt-2 list-disc list-inside
                                   text-sm text-red-700 space-y-1"
                        >

                            @foreach ($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif


        {{-- USAGE INFORMATION --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div
                class="bg-white rounded-2xl
                       border border-gray-200
                       shadow-sm p-5"
            >

                <p class="text-sm text-gray-500">
                    Complaint Types
                </p>

                <p class="text-2xl font-bold text-gray-900 mt-1">
                    {{ $division->complaint_types_count }}
                </p>

            </div>


            <div
                class="bg-white rounded-2xl
                       border border-gray-200
                       shadow-sm p-5"
            >

                <p class="text-sm text-gray-500">
                    Complaints
                </p>

                <p class="text-2xl font-bold text-gray-900 mt-1">
                    {{ $division->complaints_count }}
                </p>

            </div>

        </div>


        <form
            method="POST"
            action="{{ route(
                'customer-service.divisions.update',
                $division
            ) }}"
        >

            @csrf
            @method('PUT')


            <div
                class="bg-white rounded-2xl
                       border border-gray-200
                       shadow-sm overflow-hidden"
            >

                {{-- HEADER --}}

                <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                                   bg-blue-100 text-blue-600
                                   flex items-center justify-center"
                        >

                            <i class="fas fa-building"></i>

                        </div>

                        <div>

                            <h2 class="font-semibold text-gray-900">
                                Division Information
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Modify the details for {{ $division->name }}.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-5 sm:p-6 space-y-6">

                    {{-- NAME --}}

                    <div>

                        <label
                            for="name"
                            class="block text-sm font-semibold
                                   text-gray-700 mb-2"
                        >

                            Division Name

                            <span class="text-red-500">
                                *
                            </span>

                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $division->name) }}"
                            required
                            maxlength="255"
                            class="w-full rounded-xl
                                   border-gray-300 px-4 py-3
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                        @error('name')

                            <p class="text-sm text-red-600 mt-2">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- DESCRIPTION --}}

                    <div>

                        <label
                            for="description"
                            class="block text-sm font-semibold
                                   text-gray-700 mb-2"
                        >
                            Description
                        </label>

                        <textarea
                            name="description"
                            id="description"
                            rows="5"
                            maxlength="1000"
                            class="w-full rounded-xl
                                   border-gray-300 px-4 py-3
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                        >{{ old('description', $division->description) }}</textarea>

                        @error('description')

                            <p class="text-sm text-red-600 mt-2">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- STATUS --}}

                    <div>

                        <label
                            for="is_active"
                            class="block text-sm font-semibold
                                   text-gray-700 mb-2"
                        >
                            Status
                        </label>

                        <select
                            name="is_active"
                            id="is_active"
                            required
                            class="w-full rounded-xl
                                   border-gray-300
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                            <option
                                value="1"
                                @selected(
                                    (string) old(
                                        'is_active',
                                        $division->is_active ? '1' : '0'
                                    ) === '1'
                                )
                            >
                                Active
                            </option>

                            <option
                                value="0"
                                @selected(
                                    (string) old(
                                        'is_active',
                                        $division->is_active ? '1' : '0'
                                    ) === '0'
                                )
                            >
                                Inactive
                            </option>

                        </select>

                        <p class="text-xs text-gray-500 mt-2">
                            Inactive divisions will not be available when creating new complaint records or complaint types.
                        </p>

                        @error('is_active')

                            <p class="text-sm text-red-600 mt-2">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- WARNING --}}

                    @if (
                        $division->complaint_types_count > 0 ||
                        $division->complaints_count > 0
                    )

                        <div
                            class="p-4 rounded-xl
                                   bg-yellow-50 border border-yellow-200"
                        >

                            <div class="flex items-start gap-3">

                                <i
                                    class="fas fa-triangle-exclamation
                                           text-yellow-600 mt-0.5"
                                ></i>

                                <div>

                                    <p class="text-sm font-semibold text-yellow-800">
                                        Division is currently in use
                                    </p>

                                    <p class="text-sm text-yellow-700 mt-1">
                                        Existing records will remain associated with this division even if you mark it inactive.
                                    </p>

                                </div>

                            </div>

                        </div>

                    @endif

                </div>


                {{-- ACTIONS --}}

                <div
                    class="px-5 sm:px-6 py-5
                           bg-gray-50 border-t border-gray-100"
                >

                    <div
                        class="flex flex-col-reverse
                               sm:flex-row sm:justify-end gap-3"
                    >

                        <a
                            href="{{ route('customer-service.divisions.index') }}"
                            class="inline-flex items-center justify-center
                                   px-5 py-3 rounded-xl
                                   border border-gray-300
                                   text-gray-700 font-medium
                                   hover:bg-white transition"
                        >
                            Cancel
                        </a>


                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2
                                   px-5 py-3 rounded-xl
                                   bg-blue-600 text-white
                                   font-semibold
                                   hover:bg-blue-700 transition"
                        >

                            <i class="fas fa-floppy-disk"></i>

                            Save Changes

                        </button>

                    </div>

                </div>

            </div>

        </form>

    </div>

@endsection
