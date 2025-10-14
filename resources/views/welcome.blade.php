<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="Simple scheduling for friend groups - coordinate your time effortlessly">
    
    <title>{{ config('app.name', 'MenzaBita') }} - Schedule with Friends</title>
    
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MenzaBita">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- PWA Installation Script -->
    <script src="/pwa-install.js"></script>
</head>
<body class="bg-white text-gray-900 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="px-4 py-6 sm:px-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h1 class="text-xl font-semibold text-gray-900">MenzaBita</h1>
                </div>
                
                @if (Route::has('login'))
                    <nav class="flex space-x-2">
                        @auth
                            <a href="{{ route('groups.index') }}" class="btn-primary text-sm">
                                My Groups
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-secondary text-sm">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary text-sm">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:px-8">
            <div class="max-w-md mx-auto text-center">
                <!-- Hero Icon -->
                <div class="mx-auto w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mb-8">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>

                <!-- Hero Text -->
                <h2 class="text-3xl font-semibold text-gray-900 mb-4">
                    Schedule with Friends
                </h2>
                <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                    Find the perfect time for your group. Share your availability and see when everyone's free.
                </p>

                <!-- CTA Buttons -->
                <div class="space-y-3">
                    @guest
                        <a href="{{ route('register') }}" class="btn-primary w-full block text-center">
                            Create Your First Group
                        </a>
                        <a href="{{ route('login') }}" class="btn-secondary w-full block text-center">
                            Sign In
                        </a>
                    @else
                        <a href="{{ route('groups.index') }}" class="btn-primary w-full block text-center">
                            Go to My Groups
                        </a>
                    @endguest
                </div>

                <!-- Features -->
                <div class="mt-12 space-y-6">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 class="font-medium text-gray-900">Simple Group Creation</h3>
                            <p class="text-sm text-gray-600">Create groups and invite friends from your contacts</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 class="font-medium text-gray-900">Visual Availability</h3>
                            <p class="text-sm text-gray-600">See overlapping free times in an intuitive calendar view</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 class="font-medium text-gray-900">Mobile First</h3>
                            <p class="text-sm text-gray-600">Optimized for mobile with PWA support</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="px-4 py-6 sm:px-6">
            <div class="text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} MenzaBita. Simple scheduling for friend groups.</p>
            </div>
        </footer>
    </div>

    <!-- PWA Install Prompt (will be handled by JavaScript) -->
    <div id="pwa-install-banner" class="hidden fixed bottom-4 left-4 right-4 bg-white border border-gray-200 rounded-lg shadow-lg p-4 z-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Install MenzaBita</p>
                    <p class="text-sm text-gray-600">Get quick access from your home screen</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <button id="pwa-install-dismiss" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <button id="pwa-install-button" class="btn-primary text-sm">
                    Install
                </button>
            </div>
        </div>
    </div>
</body>
</html>
