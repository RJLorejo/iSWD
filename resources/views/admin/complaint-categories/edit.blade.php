@extends('admin.layouts.app')

@section('title','Edit Complaint Category')

@section('content')

<x-form.page-header
    title="Edit Complaint Category"
    subtitle="Update complaint category information"
/>

<div class="max-w-4xl mx-auto">

    <div class="bg-white rounded-2xl shadow border">

        <form method="POST"
              action="{{ route('admin.complaint-categories.update',$complaintCategory) }}">

            @csrf
            @method('PUT')

            <div class="p-8 grid grid-cols-2 gap-6">

                @include('admin.complaint-categories.partials.form')

            </div>

            <div class="px-8">

                <label class="flex items-center gap-3">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked(old('is_active',$complaintCategory->is_active))
                    >

                    Active Category

                </label>

            </div>

            <div class="border-t mt-8 p-6 flex justify-end gap-3">

                <a
                    href="{{ route('admin.complaint-categories.index') }}"
                    class="px-5 py-2 border rounded-xl"
                >
                    Cancel
                </a>

                <button
                    class="px-6 py-2 rounded-xl bg-sky-700 text-white"
                >
                    Update Category
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
