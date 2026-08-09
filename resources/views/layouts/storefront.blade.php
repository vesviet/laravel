<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Storefront')</title>
    @yield('meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:p-4 focus:bg-white focus:text-blue-600 focus:z-[100] focus:ring-2 focus:ring-blue-500">Skip to content</a>
    <livewire:flash-sale-banner />
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Main Navigation">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('products.index') }}" class="text-xl font-bold text-blue-600">
                            E-Commerce
                        </a>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Products
                        </a>
                        <a href="{{ route('track-order.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Track Order
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    @auth('customer')
                        <a href="{{ route('account.wishlist') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium mr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 p-1">Wishlist</a>
                        <a href="{{ route('account.orders') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium mr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 p-1">My Account</a>
                        <form method="POST" action="{{ route('account.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm font-medium mr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 p-1">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('account.login') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium mr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 p-1">Login</a>
                    @endauth
                    
                    <button type="button" aria-label="Toggle Cart" class="text-gray-500 hover:text-gray-700 relative min-w-[44px] min-h-[44px] flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md" x-data @click="$dispatch('open-cart')">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <livewire:cart-count />
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <main id="main-content" tabindex="-1" class="focus:outline-none">
        @yield('content')
    </main>

    <footer class="bg-white mt-12 border-t border-gray-200">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-base text-gray-400">
                &copy; {{ date('Y') }} E-Commerce Store. All rights reserved.
            </p>
        </div>
    </footer>

    <livewire:cart-drawer />

    @livewireScripts
</body>
</html>
