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
