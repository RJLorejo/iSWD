@extends('admin.layouts.app')

@section('title', 'Edit Department')

@section('content')

    <x-form.page-header title="" subtitle="Update department information" />

    <div class="max-w-4xl mx-auto">

        <div class="bg-white rounded-2xl shadow border">

            <form action="{{ route('admin.departments.update', $department) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="p-8 grid grid-cols-2 gap-6">

                    @include('admin.departments.partials.form')

                </div>

                @if (isset($department))
                    <div class="mt-5">

                        <label class="flex items-center gap-3 cursor-pointer">

                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', optional($department)->is_active))
                                class="w-5 h-5 rounded text-blue-600">

                            <span class="font-medium">
                                Active Department
                            </span>

                        </label>

                    </div>
                @endif
                <div class="border-t p-6 flex justify-end gap-3">

                    <a href="{{ route('admin.departments.index') }}" class="px-5 py-2 rounded-xl border">

                        Cancel

                    </a>

                    <button class="px-6 py-2 rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 text-white">

                        Update Department

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
