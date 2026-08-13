@extends('admin.layouts.app')

@section('title', 'Create Position')

@section('content')

    <x-form.page-header title="Create Position" subtitle="Register a new employee position." />

    <div class="max-w-4xl mx-auto">

        <div class="bg-white rounded-2xl shadow border">

            <form method="POST" action="{{ route('admin.positions.store') }}">

                @csrf

                <div class="p-8 grid grid-cols-2 gap-6">

                    @include('admin.positions.partials.form')

                </div>

                <div class="border-t p-6 flex justify-end gap-3">

                    <a href="{{ route('admin.positions.index') }}" class="px-5 py-2 rounded-xl border">

                        Cancel

                    </a>

                    <button class="px-6 py-2 rounded-xl bg-gradient-to-r from-blue-700 via-sky-700 to-cyan-600 text-white">

                        Create Position

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
