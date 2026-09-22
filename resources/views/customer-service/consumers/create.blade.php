@extends('customer-service.layouts.app')

@section('title', 'Register Consumer')

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

        <x-form.page-header title="Register Consumer"
            subtitle="Register a consumer and automatically create their iSWD online account." />


        {{-- ========================================================= --}}
        {{-- INFORMATION --}}
        {{-- ========================================================= --}}

        <div class="rounded-xl border border-blue-200
                   bg-blue-50 p-4">

            <div class="flex items-start gap-3">

                <div
                    class="w-9 h-9 rounded-lg
                           bg-blue-100 text-blue-600
                           flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-info"></i>
                </div>

                <div>

                    <p class="font-medium text-blue-900">
                        Online account included
                    </p>

                    <p class="text-sm text-blue-700 mt-1">
                        A consumer portal account will be created automatically.
                        After registration, a temporary password will be shown once.
                    </p>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FORM --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <form method="POST" action="{{ route('customer-service.consumers.store') }}">

                @csrf

                @include('customer-service.consumers.partials.form', [
                    'consumer' => null,
                ])

            </form>

        </x-form.card>

    </div>

@endsection
