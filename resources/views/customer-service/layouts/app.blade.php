<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#0369a1">

    <title>@yield('title', 'Customer Service') | iSWD</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    @stack('styles')

</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">

    <div
        x-data="{
            sidebarOpen: false,
            sidebarMini: false
        }"
        class="min-h-screen">

        @include('customer-service.layouts.sidebar')

        <div
            :class="sidebarMini ? 'lg:pl-20' : 'lg:pl-72'"
            class="flex min-h-screen min-w-0 flex-col transition-[padding] duration-300 ease-in-out">

            @include('customer-service.layouts.navbar')

            <main class="flex-1">

                <div class="mx-auto w-full max-w-[1600px] px-4 py-5 sm:px-6 sm:py-6 lg:px-8 lg:py-8">

                    @if (session('success'))

                        <div
                            class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">

                                    <i class="fas fa-circle-check"></i>

                                </div>

                                <div class="min-w-0 flex-1">

                                    <p class="text-sm font-semibold text-emerald-900">
                                        Action completed successfully
                                    </p>

                                    <div class="mt-1 text-sm leading-6 text-emerald-700">
                                        {!! session('success') !!}
                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif

                    @if (session('warning'))

                        <div
                            class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">

                                    <i class="fas fa-triangle-exclamation"></i>

                                </div>

                                <div class="min-w-0 flex-1">

                                    <p class="text-sm font-semibold text-amber-900">
                                        Attention required
                                    </p>

                                    <div class="mt-1 text-sm leading-6 text-amber-700">
                                        {!! session('warning') !!}
                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif

                    @if (session('error'))

                        <div
                            class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm">

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">

                                    <i class="fas fa-circle-exclamation"></i>

                                </div>

                                <div class="min-w-0 flex-1">

                                    <p class="text-sm font-semibold text-red-900">
                                        Unable to complete the action
                                    </p>

                                    <div class="mt-1 text-sm leading-6 text-red-700">
                                        {!! session('error') !!}
                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif

                    @yield('content')

                </div>

            </main>

            @include('customer-service.layouts.footer')

        </div>

    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')

</body>

</html>
