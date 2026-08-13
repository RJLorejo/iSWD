@extends('admin.layouts.app')

@section('title', 'Edit Position')

@section('content')

    <x-form.page-header title="Edit Position" subtitle="Update employee position." />

    <div class="max-w-4xl mx-auto">

        <div class="bg-white rounded-2xl shadow border">

            <form method="POST" action="{{ route('admin.positions.update', $position) }}">

                @csrf
                @method('PUT')

                <div class="p-8 grid grid-cols-2 gap-6">

                    @include('admin.positions.partials.form')

                </div>

                <div class="px-8 pb-6">

                    <label class="flex items-center gap-3 cursor-pointer">

                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $position->is_active))
                            class="w-5 h-5 rounded text-blue-600">

                        <span class="font-medium">

                            Active Position

                        </span>

                    </label>

                </div>

                <div class="border-t p-6 flex justify-end gap-3">

                    <a href="{{ route('admin.positions.index') }}" class="px-5 py-2 rounded-xl border">

                        Cancel

                    </a>

                    <button class="px-6 py-2 rounded-xl bg-gradient-to-r from-blue-700 via-sky-700 to-cyan-600 text-white">

                        Update Position

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
