@extends('customer-service.layouts.app')

@section('title', 'Register Consumer')

@section('content')

    <div class="max-w-7xl mx-auto space-y-5">

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">

                <div class="flex items-start gap-3">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-100">

                        <i class="fa-solid fa-circle-exclamation text-red-600"></i>

                    </div>

                    <div class="min-w-0">

                        <h3 class="text-sm font-semibold text-red-800">
                            Please correct the following errors:
                        </h3>

                        <ul class="mt-1.5 list-inside list-disc space-y-0.5 text-sm text-red-700">

                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>
        @endif


        <x-form.page-header title="Register Consumer" subtitle="Register a consumer and create their iSWD online account." />


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
