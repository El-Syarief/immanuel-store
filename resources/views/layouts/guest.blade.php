<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Immanuel Store') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            
            <div class="mb-6 text-center">
                <a href="/" class="flex flex-col items-center gap-2">
                    <div class="w-12 h-12 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-xl font-bold shadow-lg">
                        IS
                    </div>
                    <span class="text-xl font-bold text-gray-700 tracking-tight">IMMANUEL STORE</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Internal System. All rights reserved.
            </div>
        </div>
    </body>
</html>