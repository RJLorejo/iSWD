<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @hasSection('title')
            @yield('title') |
        @endif iSWD
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">

    <div x-data="{
        sidebarOpen: false,
        sidebarMini: false
    }" class="min-h-screen">

        @include('consumer.layouts.sidebar')

        <div :class="sidebarMini ? 'lg:ml-20' : 'lg:ml-72'"
            class="flex min-h-screen flex-col transition-[margin] duration-300 ease-in-out">

            @include('consumer.layouts.navbar')

            <main class="flex-1">

                <div class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8">

                    @if (session('success'))
                        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-600">

                                    <i class="fas fa-circle-check"></i>

                                </div>

                                <div class="min-w-0 flex-1">

                                    <p class="text-sm font-semibold text-emerald-900">
                                        Success
                                    </p>

                                    <div class="mt-1 text-sm leading-6 text-emerald-700">
                                        {{ session('success') }}
                                    </div>

                                </div>

                            </div>

                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm">

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-red-600">

                                    <i class="fas fa-circle-exclamation"></i>

                                </div>

                                <div class="min-w-0 flex-1">

                                    <p class="text-sm font-semibold text-red-900">
                                        Unable to Complete Request
                                    </p>

                                    <div class="mt-1 text-sm leading-6 text-red-700">
                                        {{ session('error') }}
                                    </div>

                                </div>

                            </div>

                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-amber-600">

                                    <i class="fas fa-triangle-exclamation"></i>

                                </div>

                                <div class="min-w-0 flex-1">

                                    <p class="text-sm font-semibold text-amber-900">
                                        Notice
                                    </p>

                                    <div class="mt-1 text-sm leading-6 text-amber-800">
                                        {{ session('warning') }}
                                    </div>

                                </div>

                            </div>

                        </div>
                    @endif

                    @yield('content')

                </div>

            </main>

            @include('consumer.layouts.footer')

        </div>

    </div>
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('scripts')



</body>

</html>
