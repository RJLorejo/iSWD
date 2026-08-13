@extends('customer-service.layouts.app')

@section('title', 'Complaint Categories')

@section('content')

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Complaint Categories
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Manage categories used when recording consumer complaints.
                </p>
            </div>

            <a href="{{ route('customer-service.complaint-categories.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                   bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600
                   text-white font-medium shadow-sm hover:shadow-md transition">

                <i class="fas fa-plus"></i>

                Add Category
            </a>

        </div>


        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            <x-admin.stat-card title="Total Categories" :value="$totalCategories" icon="fas fa-layer-group" color="blue" />

            <x-admin.stat-card title="Active" :value="$activeCategories" icon="fas fa-check-circle" color="green" />

            <x-admin.stat-card title="Inactive" :value="$inactiveCategories" icon="fas fa-ban" color="red" />

        </div>


        {{-- Success / Error Messages --}}
        @if (session('success'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800">

                <i class="fas fa-circle-check"></i>

                <span class="text-sm font-medium">
                    {{ session('success') }}
                </span>

            </div>
        @endif


        @if (session('error'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800">

                <i class="fas fa-circle-exclamation"></i>

                <span class="text-sm font-medium">
                    {{ session('error') }}
                </span>

            </div>
        @endif


        {{-- Search / Filter --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">

            <form method="GET" action="{{ route('customer-service.complaint-categories.index') }}" class="p-5">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    {{-- Search --}}
                    <div class="md:col-span-2 relative">

                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search category..."
                            class="w-full pl-11 pr-4 py-2.5 rounded-xl border-gray-300
                               focus:border-blue-500 focus:ring-blue-500">

                    </div>


                    {{-- Status --}}
                    <div>

                        <select name="status"
                            class="w-full py-2.5 rounded-xl border-gray-300
                               focus:border-blue-500 focus:ring-blue-500">

                            <option value="">
                                All Status
                            </option>

                            <option value="1" @selected(request('status') === '1')>
                                Active
                            </option>

                            <option value="0" @selected(request('status') === '0')>
                                Inactive
                            </option>

                        </select>

                    </div>


                    {{-- Buttons --}}
                    <div class="flex gap-2">

                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2
                               px-4 py-2.5 rounded-xl bg-blue-600 text-white
                               font-medium hover:bg-blue-700 transition">

                            <i class="fas fa-search"></i>

                            Search

                        </button>


                        @if (request()->hasAny(['search', 'status']))
                            <a href="{{ route('customer-service.complaint-categories.index') }}"
                                class="inline-flex items-center justify-center px-4
                                   rounded-xl border border-gray-300 text-gray-600
                                   hover:bg-gray-50">

                                <i class="fas fa-rotate-left"></i>

                            </a>
                        @endif

                    </div>

                </div>

            </form>

        </div>


        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Code
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Category
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Description
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50 transition">

                                {{-- Code --}}
                                <td class="px-6 py-4">

                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg
                                             bg-blue-50 text-blue-700 text-xs font-bold">

                                        {{ $category->code }}

                                    </span>

                                </td>


                                {{-- Name --}}
                                <td class="px-6 py-4">

                                    <div class="font-semibold text-gray-900">
                                        {{ $category->name }}
                                    </div>

                                </td>


                                {{-- Description --}}
                                <td class="px-6 py-4">

                                    <div class="max-w-md text-sm text-gray-500 truncate">

                                        {{ $category->description ?: 'No description provided.' }}

                                    </div>

                                </td>


                                {{-- Status --}}
                                <td class="px-6 py-4 text-center">

                                    @if ($category->is_active)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1
                                                 rounded-full bg-green-50 text-green-700
                                                 text-xs font-semibold">

                                            <i class="fas fa-circle text-[7px]"></i>

                                            Active

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1
                                                 rounded-full bg-red-50 text-red-700
                                                 text-xs font-semibold">

                                            <i class="fas fa-circle text-[7px]"></i>

                                            Inactive

                                        </span>
                                    @endif

                                </td>


                                {{-- Actions --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- Edit --}}
                                        <a href="{{ route('customer-service.complaint-categories.edit', $category) }}"
                                            title="Edit Category"
                                            class="w-9 h-9 inline-flex items-center justify-center
                                               rounded-lg bg-amber-50 text-amber-600
                                               hover:bg-amber-100 transition">

                                            <i class="fas fa-pen"></i>

                                        </a>


                                        {{-- Delete --}}
                                        <form
                                            action="{{ route('customer-service.complaint-categories.destroy', $category) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this complaint category?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" title="Delete Category"
                                                class="w-9 h-9 inline-flex items-center justify-center
                                                   rounded-lg bg-red-50 text-red-600
                                                   hover:bg-red-100 transition">

                                                <i class="fas fa-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="px-6 py-16 text-center">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="w-14 h-14 rounded-full bg-gray-100
                                                flex items-center justify-center mb-4">

                                            <i class="fas fa-layer-group text-gray-400 text-xl"></i>

                                        </div>

                                        <h3 class="font-semibold text-gray-900">
                                            No complaint categories found
                                        </h3>

                                        <p class="text-sm text-gray-500 mt-1">
                                            Create your first complaint category.
                                        </p>

                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if ($categories->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">

                    {{ $categories->links() }}

                </div>
            @endif

        </div>

    </div>

@endsection
