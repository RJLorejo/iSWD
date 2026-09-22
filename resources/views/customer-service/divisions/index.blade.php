@extends('customer-service.layouts.app')

@section('title', 'Divisions')

@section('content')

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div
            class="flex flex-col sm:flex-row
                   sm:items-center sm:justify-between gap-4"
        >

            <div>

                <p class="text-sm text-gray-500">
                    Complaint Management
                </p>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    Divisions
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Manage divisions used to classify and route consumer complaints.
                </p>

            </div>


            <a
                href="{{ route('customer-service.divisions.create') }}"
                class="inline-flex items-center justify-center gap-2
                       px-5 py-3 rounded-xl
                       bg-blue-600 text-white font-semibold
                       hover:bg-blue-700 transition
                       shadow-sm w-full sm:w-auto"
            >

                <i class="fas fa-plus"></i>

                Add Division

            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- FLASH MESSAGES --}}
        {{-- ========================================================= --}}

        @if (session('success'))

            <div
                class="rounded-2xl border border-green-200
                       bg-green-50 p-4"
            >

                <div class="flex items-start gap-3">

                    <i
                        class="fas fa-circle-check
                               text-green-600 mt-0.5"
                    ></i>

                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>

                </div>

            </div>

        @endif


        @if (session('error'))

            <div
                class="rounded-2xl border border-red-200
                       bg-red-50 p-4"
            >

                <div class="flex items-start gap-3">

                    <i
                        class="fas fa-circle-exclamation
                               text-red-600 mt-0.5"
                    ></i>

                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>

                </div>

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- TOTAL --}}

            <div
                class="bg-white rounded-2xl
                       border border-gray-200
                       shadow-sm p-5"
            >

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-gray-500">
                            Total Divisions
                        </p>

                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ $totalDivisions }}
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-xl
                               bg-blue-100 text-blue-600
                               flex items-center justify-center"
                    >

                        <i class="fas fa-building"></i>

                    </div>

                </div>

            </div>


            {{-- ACTIVE --}}

            <div
                class="bg-white rounded-2xl
                       border border-gray-200
                       shadow-sm p-5"
            >

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-gray-500">
                            Active
                        </p>

                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ $activeDivisions }}
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-xl
                               bg-green-100 text-green-600
                               flex items-center justify-center"
                    >

                        <i class="fas fa-circle-check"></i>

                    </div>

                </div>

            </div>


            {{-- INACTIVE --}}

            <div
                class="bg-white rounded-2xl
                       border border-gray-200
                       shadow-sm p-5"
            >

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-gray-500">
                            Inactive
                        </p>

                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ $inactiveDivisions }}
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-xl
                               bg-gray-100 text-gray-600
                               flex items-center justify-center"
                    >

                        <i class="fas fa-circle-minus"></i>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FILTERS --}}
        {{-- ========================================================= --}}

        <div
            class="bg-white rounded-2xl
                   border border-gray-200 shadow-sm p-5"
        >

            <form
                method="GET"
                action="{{ route('customer-service.divisions.index') }}"
                class="grid grid-cols-1 md:grid-cols-12 gap-4"
            >

                {{-- SEARCH --}}

                <div class="md:col-span-7">

                    <label
                        for="search"
                        class="block text-sm font-medium
                               text-gray-700 mb-2"
                    >
                        Search
                    </label>

                    <div class="relative">

                        <i
                            class="fas fa-magnifying-glass
                                   absolute left-4 top-1/2
                                   -translate-y-1/2
                                   text-gray-400"
                        ></i>

                        <input
                            type="text"
                            name="search"
                            id="search"
                            value="{{ request('search') }}"
                            placeholder="Search division name or description..."
                            class="w-full pl-11 pr-4 py-3
                                   rounded-xl border-gray-300
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                    </div>

                </div>


                {{-- STATUS --}}

                <div class="md:col-span-3">

                    <label
                        for="status"
                        class="block text-sm font-medium
                               text-gray-700 mb-2"
                    >
                        Status
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="w-full rounded-xl
                               border-gray-300
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >

                        <option value="">
                            All Statuses
                        </option>

                        <option
                            value="1"
                            @selected(request('status') === '1')
                        >
                            Active
                        </option>

                        <option
                            value="0"
                            @selected(request('status') === '0')
                        >
                            Inactive
                        </option>

                    </select>

                </div>


                {{-- ACTIONS --}}

                <div
                    class="md:col-span-2
                           flex items-end gap-2"
                >

                    <button
                        type="submit"
                        class="flex-1 h-12
                               inline-flex items-center
                               justify-center
                               rounded-xl
                               bg-blue-600 text-white
                               hover:bg-blue-700 transition"
                    >

                        <i class="fas fa-filter"></i>

                    </button>


                    <a
                        href="{{ route('customer-service.divisions.index') }}"
                        title="Clear Filters"
                        class="w-12 h-12 rounded-xl
                               border border-gray-300
                               text-gray-600
                               flex items-center justify-center
                               hover:bg-gray-50 transition"
                    >

                        <i class="fas fa-rotate-left"></i>

                    </a>

                </div>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- DIVISIONS --}}
        {{-- ========================================================= --}}

        <div
            class="bg-white rounded-2xl
                   border border-gray-200
                   shadow-sm overflow-hidden"
        >

            <div
                class="px-4 sm:px-6 py-5
                       border-b border-gray-100
                       flex items-center justify-between gap-4"
            >

                <div>

                    <h2 class="font-semibold text-gray-900">
                        Division Records
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Divisions available for complaint classification.
                    </p>

                </div>


                <span
                    class="px-3 py-1.5 rounded-full
                           bg-gray-100 text-gray-600
                           text-sm font-medium shrink-0"
                >
                    {{ $divisions->total() }}
                </span>

            </div>


            {{-- ===================================================== --}}
            {{-- MOBILE --}}
            {{-- ===================================================== --}}

            <div class="lg:hidden divide-y divide-gray-100">

                @forelse ($divisions as $division)

                    <div class="p-5">

                        <div class="flex items-start justify-between gap-4">

                            <div class="flex items-start gap-3 min-w-0">

                                <div
                                    class="w-11 h-11 rounded-xl
                                           bg-blue-50 text-blue-600
                                           flex items-center justify-center
                                           shrink-0"
                                >

                                    <i class="fas fa-building"></i>

                                </div>


                                <div class="min-w-0">

                                    <h3
                                        class="font-semibold
                                               text-gray-900 break-words"
                                    >
                                        {{ $division->name }}
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $division->complaint_types_count }}
                                        {{ Str::plural(
                                            'complaint type',
                                            $division->complaint_types_count
                                        ) }}
                                    </p>

                                </div>

                            </div>


                            @if ($division->is_active)

                                <span
                                    class="px-2.5 py-1 rounded-full
                                           bg-green-100 text-green-700
                                           text-xs font-semibold"
                                >
                                    Active
                                </span>

                            @else

                                <span
                                    class="px-2.5 py-1 rounded-full
                                           bg-gray-100 text-gray-600
                                           text-xs font-semibold"
                                >
                                    Inactive
                                </span>

                            @endif

                        </div>


                        @if ($division->description)

                            <p
                                class="text-sm text-gray-600
                                       leading-relaxed mt-4"
                            >
                                {{ $division->description }}
                            </p>

                        @else

                            <p class="text-sm text-gray-400 italic mt-4">
                                No description provided.
                            </p>

                        @endif


                        <div
                            class="mt-4 pt-4 border-t border-gray-100
                                   flex items-center gap-2"
                        >

                            <a
                                href="{{ route(
                                    'customer-service.divisions.edit',
                                    $division
                                ) }}"
                                class="flex-1 inline-flex
                                       items-center justify-center gap-2
                                       px-4 py-2.5 rounded-xl
                                       bg-blue-50 text-blue-700
                                       font-medium
                                       hover:bg-blue-100 transition"
                            >

                                <i class="fas fa-pen-to-square"></i>

                                Edit

                            </a>


                            <form
                                action="{{ route(
                                    'customer-service.divisions.destroy',
                                    $division
                                ) }}"
                                method="POST"
                                onsubmit="
                                    return confirm(
                                        'Are you sure you want to delete this division?'
                                    );
                                "
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="w-11 h-11 rounded-xl
                                           bg-red-50 text-red-600
                                           flex items-center justify-center
                                           hover:bg-red-100 transition"
                                    title="Delete Division"
                                >

                                    <i class="fas fa-trash"></i>

                                </button>

                            </form>

                        </div>

                    </div>

                @empty

                    <div class="py-16 text-center px-5">

                        <div
                            class="w-16 h-16 mx-auto rounded-2xl
                                   bg-gray-100 text-gray-400
                                   flex items-center justify-center"
                        >

                            <i class="fas fa-building text-2xl"></i>

                        </div>

                        <h3 class="font-semibold text-gray-900 mt-4">
                            No divisions found
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            No division records match your current filters.
                        </p>

                    </div>

                @endforelse

            </div>


            {{-- ===================================================== --}}
            {{-- DESKTOP --}}
            {{-- ===================================================== --}}

            <div class="hidden lg:block overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-100">

                    <thead class="bg-gray-50">

                        <tr>

                            <th
                                class="px-6 py-4 text-left
                                       text-xs font-semibold text-gray-500
                                       uppercase tracking-wider"
                            >
                                Division
                            </th>

                            <th
                                class="px-6 py-4 text-left
                                       text-xs font-semibold text-gray-500
                                       uppercase tracking-wider"
                            >
                                Description
                            </th>

                            <th
                                class="px-6 py-4 text-center
                                       text-xs font-semibold text-gray-500
                                       uppercase tracking-wider"
                            >
                                Complaint Types
                            </th>

                            <th
                                class="px-6 py-4 text-center
                                       text-xs font-semibold text-gray-500
                                       uppercase tracking-wider"
                            >
                                Complaints
                            </th>

                            <th
                                class="px-6 py-4 text-center
                                       text-xs font-semibold text-gray-500
                                       uppercase tracking-wider"
                            >
                                Status
                            </th>

                            <th
                                class="px-6 py-4 text-center
                                       text-xs font-semibold text-gray-500
                                       uppercase tracking-wider"
                            >
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($divisions as $division)

                            <tr class="hover:bg-gray-50 transition">

                                {{-- DIVISION --}}

                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="w-10 h-10 rounded-xl
                                                   bg-blue-50 text-blue-600
                                                   flex items-center justify-center
                                                   shrink-0"
                                        >

                                            <i class="fas fa-building"></i>

                                        </div>

                                        <p class="font-semibold text-gray-900">
                                            {{ $division->name }}
                                        </p>

                                    </div>

                                </td>


                                {{-- DESCRIPTION --}}

                                <td class="px-6 py-4">

                                    <p
                                        class="text-sm text-gray-600
                                               max-w-md line-clamp-2"
                                    >
                                        {{ $division->description
                                            ?: 'No description provided.' }}
                                    </p>

                                </td>


                                {{-- TYPES --}}

                                <td class="px-6 py-4 text-center">

                                    <span
                                        class="inline-flex
                                               px-3 py-1 rounded-full
                                               bg-blue-50 text-blue-700
                                               text-sm font-semibold"
                                    >
                                        {{ $division->complaint_types_count }}
                                    </span>

                                </td>


                                {{-- COMPLAINTS --}}

                                <td class="px-6 py-4 text-center">

                                    <span
                                        class="inline-flex
                                               px-3 py-1 rounded-full
                                               bg-gray-100 text-gray-700
                                               text-sm font-semibold"
                                    >
                                        {{ $division->complaints_count }}
                                    </span>

                                </td>


                                {{-- STATUS --}}

                                <td class="px-6 py-4 text-center">

                                    @if ($division->is_active)

                                        <span
                                            class="inline-flex items-center gap-1.5
                                                   px-3 py-1 rounded-full
                                                   bg-green-100 text-green-700
                                                   text-xs font-semibold"
                                        >

                                            <i class="fas fa-circle-check"></i>

                                            Active

                                        </span>

                                    @else

                                        <span
                                            class="inline-flex items-center gap-1.5
                                                   px-3 py-1 rounded-full
                                                   bg-gray-100 text-gray-600
                                                   text-xs font-semibold"
                                        >

                                            <i class="fas fa-circle-minus"></i>

                                            Inactive

                                        </span>

                                    @endif

                                </td>


                                {{-- ACTIONS --}}

                                <td class="px-6 py-4">

                                    <div
                                        class="flex items-center
                                               justify-center gap-2"
                                    >

                                        <a
                                            href="{{ route(
                                                'customer-service.divisions.edit',
                                                $division
                                            ) }}"
                                            class="w-9 h-9 rounded-lg
                                                   bg-blue-50 text-blue-600
                                                   flex items-center justify-center
                                                   hover:bg-blue-100 transition"
                                            title="Edit Division"
                                        >

                                            <i class="fas fa-pen-to-square"></i>

                                        </a>


                                        <form
                                            action="{{ route(
                                                'customer-service.divisions.destroy',
                                                $division
                                            ) }}"
                                            method="POST"
                                            onsubmit="
                                                return confirm(
                                                    'Are you sure you want to delete this division?'
                                                );
                                            "
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="w-9 h-9 rounded-lg
                                                       bg-red-50 text-red-600
                                                       flex items-center justify-center
                                                       hover:bg-red-100 transition"
                                                title="Delete Division"
                                            >

                                                <i class="fas fa-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="px-6 py-16 text-center"
                                >

                                    <i
                                        class="fas fa-building
                                               text-3xl text-gray-300"
                                    ></i>

                                    <h3
                                        class="font-semibold
                                               text-gray-900 mt-4"
                                    >
                                        No divisions found
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        No division records match your filters.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}

            @if ($divisions->hasPages())

                <div
                    class="px-4 sm:px-6 py-4
                           border-t border-gray-100"
                >

                    {{ $divisions->links() }}

                </div>

            @endif

        </div>

    </div>

@endsection
