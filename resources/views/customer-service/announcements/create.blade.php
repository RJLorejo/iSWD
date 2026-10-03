@extends('customer-service.layouts.app')

@section('title', 'Create Announcement')

@section('content')

    <div class="space-y-5">

        @if ($errors->any())

            <div class="rounded-xl border border-red-200 bg-red-50 p-4">

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-9 w-9 shrink-0
                               items-center justify-center
                               rounded-lg bg-red-100">

                        <i class="fa-solid fa-circle-exclamation text-red-600"></i>

                    </div>

                    <div>

                        <h3 class="font-semibold text-red-800">
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

        <x-form.page-header
            title="Create Service Announcement"
            subtitle="Prepare a service advisory or announcement for iSWD consumers." />

        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">

            <div class="flex items-start gap-3">

                <div
                    class="flex h-9 w-9 shrink-0
                           items-center justify-center
                           rounded-lg bg-blue-100 text-blue-600">

                    <i class="fa-solid fa-file-pen"></i>

                </div>

                <div>

                    <p class="font-medium text-blue-900">
                        New announcements start as drafts
                    </p>

                    <p class="mt-0.5 text-sm text-blue-700">
                        Consumers will not see this announcement until you review and publish it.
                    </p>

                </div>

            </div>

        </div>

        <x-form.card>

            <form
                method="POST"
                action="{{ route('customer-service.announcements.store') }}">

                @csrf

                @include('customer-service.announcements.partials.form', [
                    'serviceAnnouncement' => null,
                ])

            </form>

        </x-form.card>

    </div>

@endsection
