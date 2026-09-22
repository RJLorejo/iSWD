@extends('customer-service.layouts.app')

@section('title', 'Create Division')

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
                Create Division
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Add a division that can be used to organize complaint types.
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


        <form
            method="POST"
            action="{{ route('customer-service.divisions.store') }}"
        >

            @csrf


            <div
                class="bg-white rounded-2xl
                       border border-gray-200
                       shadow-sm overflow-hidden"
            >

                {{-- CARD HEADER --}}

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
                                Enter the basic information for this division.
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
                            value="{{ old('name') }}"
                            required
                            maxlength="255"
                            placeholder="Example: Engineering Operation"
                            class="w-full rounded-xl
                                   border-gray-300 px-4 py-3
                                   focus:border-blue-500
                                   focus:ring-blue-500
                                   @error('name')
                                       border-red-400
                                   @enderror"
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
                            placeholder="Describe the responsibilities or complaint types handled by this division."
                            class="w-full rounded-xl
                                   border-gray-300 px-4 py-3
                                   focus:border-blue-500
                                   focus:ring-blue-500
                                   @error('description')
                                       border-red-400
                                   @enderror"
                        >{{ old('description') }}</textarea>

                        <div
                            class="flex flex-col sm:flex-row
                                   sm:items-center sm:justify-between
                                   gap-1 mt-2"
                        >

                            @error('description')

                                <p class="text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @else

                                <p class="text-xs text-gray-500">
                                    Optional. Briefly explain what this division handles.
                                </p>

                            @enderror

                            <p class="text-xs text-gray-400">
                                Maximum 1000 characters
                            </p>

                        </div>

                    </div>


                    {{-- DEFAULT STATUS --}}

                    <div
                        class="p-4 rounded-xl
                               bg-green-50 border border-green-100"
                    >

                        <div class="flex items-start gap-3">

                            <i
                                class="fas fa-circle-check
                                       text-green-600 mt-0.5"
                            ></i>

                            <div>

                                <p class="text-sm font-semibold text-green-800">
                                    Active by default
                                </p>

                                <p class="text-sm text-green-700 mt-1">
                                    New divisions are automatically active and available when creating complaint types.
                                </p>

                            </div>

                        </div>

                    </div>

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

                            Create Division

                        </button>

                    </div>

                </div>

            </div>

        </form>

    </div>

@endsection
