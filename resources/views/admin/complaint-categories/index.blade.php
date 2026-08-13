@extends('admin.layouts.app')

@section('title', 'Complaint Categories')

@section('content')

    <x-form.page-header title="Complaint Categories" subtitle="Manage complaint categories" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <x-admin.stat-card title="Total Categories" :value="$totalCategories" icon="fa-solid fa-folder" />

        <x-admin.stat-card title="Active" :value="$activeCategories" icon="fa-solid fa-check-circle" />

        <x-admin.stat-card title="Inactive" :value="$inactiveCategories" icon="fa-solid fa-circle-xmark" />

        <x-admin.stat-card title="Complaints" :value="$totalComplaints" icon="fa-solid fa-circle-exclamation" />

    </div>

    <div class="bg-white rounded-2xl shadow border">

        <div class="p-6 flex justify-between items-center">

            <form>

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search category..."
                    class="rounded-xl border-gray-300 w-80">

            </form>

            <a href="{{ route('admin.complaint-categories.create') }}" class="bg-sky-700 text-white px-5 py-2 rounded-xl">

                + New Category

            </a>

        </div>

        <table class="w-full">

            <thead class="bg-gray-100">

                <tr>

                    <th class="p-4 text-left">Code</th>

                    <th>Name</th>

                    <th>Complaints</th>

                    <th>Status</th>

                    <th width="180">Actions</th>

                </tr>

            </thead>

            <tbody>

                @forelse($categories as $category)
                    <tr class="border-t hover:bg-gray-50 text-center">

                        <td class="p-4 text-left">

                            <span class="font-semibold">

                                {{ $category->code }}

                            </span>

                        </td>

                        <td>

                            {{ $category->name }}

                        </td>

                        <td>

                            {{ $category->complaints_count }}

                        </td>

                        <td>

                            @if ($category->is_active)
                                <span class="text-green-600 font-semibold">

                                    Active

                                </span>
                            @else
                                <span class="text-red-600 font-semibold">

                                    Inactive

                                </span>
                            @endif

                        </td>

                                <td>

                                    <div class="flex items-center gap-2">

                                        <a href="{{ route('admin.complaint-categories.show', $category) }}"
                                            class="w-10 h-10 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center hover:bg-sky-200">

                                            <i class="fa-solid fa-eye"></i>

                                        </a>

                                        <a href="{{ route('admin.complaint-categories.edit', $category) }}"
                                            class="w-10 h-10 rounded-lg bg-yellow-100 text-yellow-700 flex items-center justify-center hover:bg-yellow-200">

                                            <i class="fa-solid fa-pen"></i>

                                        </a>

                                        <form method="POST" action="{{ route('admin.complaint-categories.destroy', $category) }}"
                                            onsubmit="return confirm('Delete this category?')">

                                            @csrf
                                            @method('DELETE')

                                            <button class="w-10 h-10 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">

                                                <i class="fa-solid fa-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="text-center p-8">

                            No complaint categories found.

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

        <div class="p-6">

            {{ $categories->links() }}

        </div>

    </div>

@endsection
