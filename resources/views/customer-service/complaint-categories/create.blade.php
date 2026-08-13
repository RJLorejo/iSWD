@extends('customer-service.layouts.app')

@section('title', 'Add Complaint Category')

@section('content')

    <div class="max-w-3xl mx-auto space-y-6">

        <x-form.page-header title="Add Complaint Category"
            subtitle="Create a category that Customer Service can use when recording complaints." />


        <form action="{{ route('customer-service.complaint-categories.store') }}" method="POST" class="space-y-6">

            @csrf


            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <h3 class="font-semibold text-gray-900">
                        Category Information
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        The system will automatically generate the category code.
                    </p>

                </div>


                <div class="p-6 space-y-5">

                    <x-form.input label="Category Name" name="name" :value="old('name')" placeholder="e.g. Water Leak"
                        required />


                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>

                        <textarea name="description" rows="4" placeholder="Describe this complaint category..."
                            class="w-full rounded-xl border-gray-300
                               focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>

                        @error('description')
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

                    Save Category

                </button>

            </div>

        </form>

    </div>

@endsection
