<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="iSWD is an AI-Assisted Consumer Complaint Management and Service Support System for Sagay Water District."
    >

    <meta name="theme-color" content="#0369a1">

    <title>
        iSWD | Sagay Water District
    </title>

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/logo/logo.png') }}"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            overflow-x: hidden;
        }

        ::selection {
            background: #0ea5e9;
            color: #ffffff;
        }
    </style>
</head>

<body class="min-h-screen bg-white font-sans text-slate-800 antialiased">


    @yield('content')

</body>

</html>
