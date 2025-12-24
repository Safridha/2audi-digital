<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '2Audi Digital Printing') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased bg-white min-h-screen flex flex-col justify-center items-center">
    
    <!-- Logo -->
    <div class="mb-4">
        <a href="/" class="focus:outline-none focus:ring-0">
            <img src="{{ asset('images/logo.png') }}" alt="2 Audi Digital" class="w-28 h-auto rounded shadow-md">
        </a>
    </div>

    <!-- Card -->
    <div class="w-full sm:max-w-md bg-blue-200 shadow-2xl rounded-xl p-6">
        <h2 class="text-center text-2xl font-bold text-blue-900 mb-4">Login</h2>
        {{ $slot }}
    </div>
</body>
</html>
