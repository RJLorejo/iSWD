<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body class="bg-slate-100 font-sans">

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

    {{-- Mobile Overlay --}}
    <div
        x-show="sidebarOpen"
        x-transition
        class="fixed inset-0 bg-black/40 z-40 lg:hidden"
        @click="sidebarOpen=false">
    </div>

    {{-- Sidebar --}}
    @include('components.sidebar')

    <div class="flex flex-col flex-1 min-h-screen">

        {{-- Top Navigation --}}
        @include('components.topbar')

        {{-- Main --}}
        <main class="flex-1 p-4 md:p-6 lg:p-8">

            @yield('content')

        </main>

        {{-- Footer --}}
        @include('components.footer')

    </div>

</div>

</body>

</html>
