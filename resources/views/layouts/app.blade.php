<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#2563eb">
        <meta name="description" content="Simple scheduling for friend groups - coordinate your time effortlessly">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- PWA Meta Tags -->
        <link rel="manifest" href="/manifest.json">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="MenzaBita">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- PWA Installation Script -->
        <script src="/pwa-install.js"></script>
        <script>
            // Connect navigation button to PWA installer
            document.addEventListener('DOMContentLoaded', function() {
                const navInstallBtn = document.getElementById('nav-pwa-install-btn');
                
                if (navInstallBtn && window.pwaInstaller) {
                    navInstallBtn.addEventListener('click', function() {
                        window.pwaInstaller.installApp();
                    });

                    // Show/hide navigation button based on install availability
                    window.addEventListener('beforeinstallprompt', function() {
                        navInstallBtn.classList.remove('hidden');
                    });

                    window.addEventListener('appinstalled', function() {
                        navInstallBtn.classList.add('hidden');
                    });

                    // Check if already installed
                    if (window.matchMedia('(display-mode: standalone)').matches || 
                        window.navigator.standalone === true) {
                        navInstallBtn.classList.add('hidden');
                    }
                }
            });
        </script>
    </body>
</html>
