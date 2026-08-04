<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>@yield('title') | KnowledgeRetain AI</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body>

<div class="wrapper">

    {{-- Sidebar --}}
    @include('components.sidebar')

    <div class="main-wrapper">

        {{-- Navbar --}}
        @include('components.navbar')

        {{-- Main Content --}}
        <main class="content">

            @yield('content')

        </main>

        {{-- Footer --}}
        @include('components.footer')

    </div>

</div>

</body>

</html>
