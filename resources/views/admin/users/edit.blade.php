@extends('admin.layouts.app')

@section('title', 'Edit Employee')

@section('content')

    <x-form.page-header title="" subtitle="Update employee information" />

    <div class="max-w-5xl mx-auto">

        <div class="bg-white rounded-3xl shadow">

            <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">

                @csrf

                @method('PUT')

                <div class="p-8 grid grid-cols-1 lg:grid-cols-2 gap-6">

                    @include('admin.users.partials.form')

                    <div class="lg:col-span-2">

                        <label class="flex items-center gap-3">

                            <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>

                            Active Account

                        </label>

                    </div>

                </div>

                <div class="border-t p-6 flex justify-end gap-3">

                    <a href="{{ route('admin.users.index') }}" class="px-6 py-3 rounded-xl border">

                        Cancel

                    </a>

                    <button class="px-6 py-3 rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 text-white">

                        Save Changes

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
