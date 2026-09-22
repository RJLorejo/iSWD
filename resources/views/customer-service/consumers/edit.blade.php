@extends('customer-service.layouts.app')

@section('title', 'Edit Consumer')

@section('content')

    <div class="space-y-6">

        {{-- ========================================================= --}}
        {{-- VALIDATION ERRORS --}}
        {{-- ========================================================= --}}

        @if ($errors->any())

            <div class="rounded-xl border border-red-200 bg-red-50 p-5">

                <div class="flex items-start gap-3">

                    <div
                        class="w-9 h-9 rounded-lg
                               bg-red-100 flex items-center
                               justify-center shrink-0">

                        <i class="fa-solid fa-circle-exclamation text-red-600"></i>

                    </div>


                    <div>

                        <h3 class="font-semibold text-red-800">
                            Please correct the following errors:
                        </h3>

                        <ul
                            class="mt-2 text-sm text-red-700
                                   list-disc list-inside space-y-1">

                            @foreach ($errors->all() as $error)
                                <li>
                                    {{ $error }}
                                </li>
                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- PAGE HEADER --}}
        {{-- ========================================================= --}}

        <x-form.page-header title="Edit Consumer"
            subtitle="Update consumer information, online access, address, and password." />


        {{-- ========================================================= --}}
        {{-- FORM --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <form method="POST"
                action="{{ route('customer-service.consumers.update', $consumer) }}">

                @csrf
                @method('PUT')


                @include('customer-service.consumers.partials.form', [
                    'consumer' => $consumer,
                ])

            </form>

        </x-form.card>

    </div>

@endsection
