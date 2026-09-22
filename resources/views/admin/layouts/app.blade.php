<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title') | iSWD
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    @stack('styles')

</head>


<body class="bg-slate-100">

    <div x-data="{
        sidebarOpen: false,
        sidebarMini: false
    }" class="flex min-h-screen">

        @include('admin.layouts.sidebar')


        <div :class="sidebarMini ? 'lg:ml-20' : 'lg:ml-72'"
            class="flex-1 flex flex-col
                   min-w-0
                   transition-all duration-300">

            @include('admin.layouts.navbar')


            <main class="flex-1 p-4 sm:p-6 lg:p-8">


                {{-- Success Message --}}

                @if (session('success'))
                    <div
                        class="mb-6 rounded-xl
                               bg-green-50
                               border border-green-200
                               text-green-800
                               p-4">

                        <div class="flex items-start gap-3">

                            <i
                                class="fa-solid fa-circle-check
                                       text-green-600 mt-0.5"></i>

                            <div>
                                {{ session('success') }}
                            </div>

                        </div>

                    </div>
                @endif


                {{-- Error Message --}}

                @if (session('error'))
                    <div
                        class="mb-6 rounded-xl
                               bg-red-50
                               border border-red-200
                               text-red-800
                               p-4">

                        <div class="flex items-start gap-3">

                            <i
                                class="fa-solid fa-circle-exclamation
                                       text-red-600 mt-0.5"></i>

                            <div>
                                {{ session('error') }}
                            </div>

                        </div>

                    </div>
                @endif


                @yield('content')

            </main>


            @include('admin.layouts.footer')

        </div>

    </div>


    @stack('scripts')

</body>

</html>
