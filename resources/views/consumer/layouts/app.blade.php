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
