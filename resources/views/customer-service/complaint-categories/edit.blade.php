@extends('customer-service.layouts.app')

@section('title', 'Edit Complaint Category')

@section('content')

    <div class="max-w-3xl mx-auto space-y-6">

        <x-form.page-header title="Edit Complaint Category" subtitle="Update the complaint category information." />


        <form action="{{ route('customer-service.complaint-categories.update', $complaintCategory) }}" method="POST"
            class="space-y-6">

            @csrf
            @method('PUT')


            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <h3 class="font-semibold text-gray-900">
                        Category Information
                    </h3>

                </div>


                <div class="p-6 space-y-5">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <x-form.input label="Category Code" name="code" :value="$complaintCategory->code" disabled />


                        <x-form.input label="Category Name" name="name" :value="old('name', $complaintCategory->name)" required />

                    </div>


                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>

                        <textarea name="description" rows="4"
                            class="w-full rounded-xl border-gray-300
                               focus:border-blue-500 focus:ring-blue-500">{{ old('description', $complaintCategory->description) }}</textarea>

                        @error('description')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Status --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>

                        <select name="is_active"
                            class="w-full rounded-xl border-gray-300
                               focus:border-blue-500 focus:ring-blue-500"
                            required>

                            <option value="1" @selected(old('is_active', $complaintCategory->is_active) == 1)>
                                Active
                            </option>

                            <option value="0" @selected(old('is_active', $complaintCategory->is_active) == 0)>
                                Inactive
                            </option>

                        </select>

                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </x-form.card>


            <div class="flex justify-end gap-3">

                <a href="{{ route('customer-service.complaint-categories.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-gray-300
                       text-gray-700 hover:bg-gray-50">

                    <i class="fas fa-arrow-left mr-2"></i>

                    Cancel

                </a>


                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-blue-600 text-white
                       font-medium hover:bg-blue-700 transition">

                    <i class="fas fa-save mr-2"></i>

                    Save Changes

                </button>

            </div>

        </form>

    </div>

@endsection
