@extends('admin.layouts.app')

@section('title', 'Complaint Category Details')

@section('content')

    <x-form.page-header title="Complaint Category" subtitle="View category information" />

    <div class="max-w-5xl mx-auto">

        <div class="bg-white rounded-2xl shadow border">

            <div class="p-8 grid grid-cols-2 gap-6">

                <x-form.readonly label="Category Code" :value="$complaintCategory->code" />

                <x-form.readonly label="Category Name" :value="$complaintCategory->name" />

                <x-form.readonly label="Total Complaints" :value="$complaintCategory->complaints_count" />

                <x-form.readonly label="Status" :value="$complaintCategory->is_active ? 'Active' : 'Inactive'" />

                <div class="col-span-2">

                    <label class="font-semibold block mb-2">

                        Description

                    </label>

                    <div class="rounded-xl border bg-gray-50 p-4">

                        {{ $complaintCategory->description ?: 'No description available.' }}

                    </div>

                </div>

                <x-form.readonly label="Created" :value="$complaintCategory->created_at->format('F d, Y h:i A')" />

                <x-form.readonly label="Updated" :value="$complaintCategory->updated_at->format('F d, Y h:i A')" />

            </div>

            <div class="border-t p-6 flex justify-end gap-3">

                <a href="{{ route('admin.complaint-categories.index') }}" class="border px-5 py-2 rounded-xl">

                    Back

                </a>

                <a href="{{ route('admin.complaint-categories.edit', $complaintCategory) }}"
                    class="bg-sky-700 text-white px-5 py-2 rounded-xl">

                    Edit

                </a>

            </div>

        </div>

    </div>

@endsection
