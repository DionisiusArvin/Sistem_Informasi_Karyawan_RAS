@php
    $sidebarCollapsed = isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] === 'true';
@endphp
<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
    :class="{ 'dark': darkMode }"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <style>[x-cloak] { display: none !important; }</style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div 
            x-data="{ sidebarOpen: false, sidebarCollapsed: {{ $sidebarCollapsed ? 'true' : 'false' }} }"
            @keydown.escape.window="sidebarOpen = false"
            class="min-h-screen bg-gray-100 dark:bg-gray-900"
        >
            <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
                
                @include('layouts.partials.sidebar')

                <div class="flex-1 flex flex-col overflow-hidden">
                    
                    @include('layouts.partials.header')

                    <main class="flex-1 overflow-x-hidden overflow-y-auto">
                        <div class="container mx-auto px-6 py-8">
                            
                            @isset($header)
                                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                                    {{ $header }}
                                </h2>
                            @endisset

                            {{ $slot }}

                        </div>
                    </main>

                </div>
            </div>
             @stack('scripts')
        </div>
    </body>
</html>