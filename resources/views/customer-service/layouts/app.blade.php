<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') | iSWD</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    @stack('styles')

</head>

<body class="bg-slate-100">

    <div x-data="{
        sidebarOpen: false,
        sidebarMini: false
    }" class="flex min-h-screen">

        @include('customer-service.layouts.sidebar')

        <div :class="sidebarMini ? 'lg:ml-20' : 'lg:ml-72'"
            class="flex-1 flex flex-col transition-all duration-300 relative z-0">

            @include('customer-service.layouts.navbar')

            <main class="flex-1 p-8">

                @if (session('success'))
                    <div class="mb-6">

                        <div class="rounded-xl bg-green-100 border border-green-300 text-green-700 p-4">

                            {!! session('success') !!}

                        </div>

                    </div>
                @endif

                @yield('content')

            </main>

            @include('customer-service.layouts.footer')

        </div>

    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')
</body>

</html>
