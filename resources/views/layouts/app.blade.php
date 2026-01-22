<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('page-title', 'Dashboard') - Exam Portal</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
        @stack('css')
    </head>
    <body class="font-sans antialiased h-full overflow-hidden">
        @auth
            <x-sidebar />
        @else
            <div class="min-h-screen bg-gray-100">
                @include('layouts.navigation')

                <main>
                    @yield('content')
                </main>
            </div>
        @endauth
        
        @stack('modals')
        @yield('modals')
    </body>
</html>
