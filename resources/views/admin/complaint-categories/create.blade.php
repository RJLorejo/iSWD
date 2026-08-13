@extends('admin.layouts.app')

@section('title', 'Create Complaint Category')

@section('content')

    <x-form.page-header title="Create Complaint Category" subtitle="Add a new complaint category" />

    <div class="max-w-4xl mx-auto">

        <div class="bg-white rounded-2xl shadow border">

            <form method="POST" action="{{ route('admin.complaint-categories.store') }}">

                @csrf

                <div class="p-8 grid grid-cols-2 gap-6">

                    @include('admin.complaint-categories.partials.form')

                </div>

                <div class="border-t p-6 flex justify-end gap-3">

                    <a href="{{ route('admin.complaint-categories.index') }}" class="px-5 py-2 rounded-xl border">

                        Cancel

                    </a>

                    <button class="px-6 py-2 rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 text-white">

                        Save Category

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
