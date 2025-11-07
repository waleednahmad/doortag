<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="tallstackui_darkTheme()">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DoorTag - Shippers</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free/css/all.min.css') }}">




    <tallstackui:script />
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased" x-cloak x-data="{ name: @js(auth()->user()->name) }" x-on:name-updated.window="name = $event.detail.name"
    x-bind:class="{ 'dark bg-gray-800': darkTheme, 'bg-gray-100': !darkTheme }">

    <!-- Global Full Page Loader -->
    <div id="global-loader"
        class="fixed inset-0 bg-gradient-to-br from-blue-900/30 via-gray-900/50 to-black/60 backdrop-blur-sm flex items-center justify-center z-[9999] transition-all duration-300"
        style="display: none;">
        <div
            class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-2xl p-8 flex flex-col items-center space-y-6 shadow-2xl max-w-md w-full mx-4 border border-white/20 dark:border-gray-700/50">
            <!-- Modern Spinner -->
            <div class="relative">
                <div
                    class="w-16 h-16 border-4 border-blue-200 dark:border-blue-800 rounded-full animate-spin border-t-blue-600 dark:border-t-blue-400">
                </div>
                <div
                    class="absolute inset-0 w-16 h-16 border-4 border-transparent rounded-full animate-ping border-t-blue-400/30">
                </div>
            </div>

            <!-- Content -->
            <div class="text-center space-y-2">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Getting Shipping Quotes
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Please wait while we calculate the
                    best rates for you...</p>

                <!-- Progress indicator -->
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-4">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-1.5 rounded-full animate-pulse"
                        style="width: 70%"></div>
                </div>
            </div>
        </div>
    </div>

    <x-layout>
        <x-slot:top>
            <x-dialog />
            <x-toast />
        </x-slot:top>
        <x-slot:header>
            <x-layout.header>
                <x-slot:left>
                    <x-theme-switch />
                </x-slot:left>
                <x-slot:right>
                    <x-dropdown>
                        <x-slot:action>
                            <div>
                                <button class="cursor-pointer" x-on:click="show = !show">
                                    <span class="text-base font-semibold text-primary-500" x-text="name"></span>
                                </button>
                            </div>
                        </x-slot:action>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown.items :text="__('Profile')" :href="route('user.profile')" />
                            <x-dropdown.items :text="__('Logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();" separator />
                        </form>
                    </x-dropdown>
                </x-slot:right>
            </x-layout.header>
        </x-slot:header>
        <x-slot:menu>
            <x-side-bar>
                <div class=" space-y-2 text-black dark:text-white bg-gray-100 dark:bg-gray-800">
                    <!-- Sidebar -->
                    <div class="flex items-center justify-center">
                        <div
                            class="bg-gray-500 dark:bg-gray-800 rounded flex items-center justify-center w-full h-[130px]">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="logo"
                                class="w-[85%] h-full object-contain" />
                        </div>
                    </div>
                    <!-- Menu Items -->
                    <nav class="w-full">
                        <!-- Repeat this block for each menu item -->
                        <a href="{{ route('shipping.shipengine.index') }}" @class([
                            'group h-[70px] flex items-center gap-4 px-4 rounded-md cursor-pointer transition hover:bg-white dark:hover:bg-gray-700 bg-transparent relative',
                            'bg-white dark:bg-gray-700' => request()->routeIs(
                                'shipping.shipengine.index'),
                        ])>
                            <div class="w-[70px] h-[70px]">
                                <img src="{{ asset('assets/icons/menu-ship.png') }}" alt="Ship"
                                    class="w-full h-full object-contain" />
                            </div>
                            <span class="text-lg font-bold">Ship</span>
                        </a>
                        {{-- <a href="{{ route('shipping.index') }}" @class([
                            'group h-[70px] flex items-center gap-4 px-4 rounded-md cursor-pointer transition hover:bg-white dark:hover:bg-gray-700 bg-transparent relative',
                            'bg-white dark:bg-gray-700' => request()->routeIs('shipping.index'),
                        ])>
                            <div class="w-[70px] h-[70px]">
                                <img src="{{ asset('assets/icons/menu-ship.png') }}" alt="Ship"
                                    class="w-full h-full object-contain" />
                            </div>
                            <span class="text-lg font-bold">Ship</span>
                        </a> --}}

                        <a href="{{ route('shipments.index') }}" @class([
                            'group h-[70px] flex items-center gap-4 px-4 rounded-md cursor-pointer transition hover:bg-white dark:hover:bg-gray-700 bg-transparent relative',
                            'bg-white dark:bg-gray-700' => request()->routeIs('shipments.index'),
                        ])>
                            <div class="w-[70px] h-[70px]">
                                <img src="{{ asset('assets/icons/menu-rates.png') }}" alt="Rates"
                                    class="w-full h-full object-contain" />
                            </div>
                            <div
                                class="absolute inset-0 transition-opacity hover:opacity-0 bg-gray-100/50 dark:bg-gray-800/50">
                            </div>
                            <span class="text-lg font-bold">Shipments</span>
                        </a>

                        <a href="#" @class([
                            'group h-[70px] flex items-center gap-4 px-4 rounded-md cursor-pointer transition hover:bg-white dark:hover:bg-gray-700 bg-transparent relative',
                            'bg-white dark:bg-gray-700' => false,
                        ])>
                            <div class="w-[70px] h-[70px]">
                                <img src="/assets/icons/menu-reports.png" alt="Reports"
                                    class="w-full h-full object-contain" />
                            </div>
                            <div
                                class="absolute inset-0 transition-opacity hover:opacity-0 bg-gray-100/50 dark:bg-gray-800/50">
                            </div>
                            <span class="text-lg font-bold">Reports</span>
                        </a>

                        <a href="#" @class([
                            'group h-[70px] flex items-center gap-4 px-4 rounded-md cursor-pointer transition hover:bg-white dark:hover:bg-gray-700 bg-transparent relative',
                            'bg-white dark:bg-gray-700' => false,
                        ])>
                            <div class="w-[70px] h-[70px]">
                                <img src="/assets/icons/menu-settings.png" alt="Settings"
                                    class="w-full h-full object-contain" />
                            </div>
                            <div
                                class="absolute inset-0 transition-opacity hover:opacity-0 bg-gray-100/50 dark:bg-gray-800/50">
                            </div>
                            <span class="text-lg font-bold">Settings</span>
                        </a>

                        <a href="#" @class([
                            'group h-[70px] flex items-center gap-4 px-4 rounded-md cursor-pointer transition hover:bg-white dark:hover:bg-gray-700 bg-transparent relative',
                            'bg-white dark:bg-gray-700' => false,
                        ])>
                            <div class="w-[70px] h-[70px]">
                                <img src="/assets/icons/menu-support.png" alt="Support"
                                    class="w-full h-full object-contain" />
                            </div>
                            <div
                                class="absolute inset-0 transition-opacity hover:opacity-0 bg-gray-100/50 dark:bg-gray-800/50">
                            </div>
                            <span class="text-lg font-bold">Support</span>
                        </a>

                        <a href="{{ route('logout') }}" @class([
                            'group h-[70px] flex items-center gap-4 px-4 rounded-md cursor-pointer transition hover:bg-white dark:hover:bg-gray-700 bg-transparent relative',
                            'bg-white dark:bg-gray-700' => false,
                        ])>
                            <div class="w-[70px] h-[70px]">
                                <img src="/assets/icons/menu-logout.png" alt="Logout"
                                    class="w-full h-full object-contain" />
                            </div>
                            <div
                                class="absolute inset-0 transition-opacity hover:opacity-0 bg-gray-100/50 dark:bg-gray-800/50">
                            </div>
                            <span class="text-lg font-bold">Logout</span>
                        </a>
                    </nav>

                    <!-- Footer -->
                    <div
                        class="text-sm text-gray-500 dark:text-gray-400 text-center space-y-1 px-2 mt-[1.38em] mb-4 bg-gray-100 dark:bg-gray-800">
                        <p>© 2014–2025 – All Rights Reserved</p>
                        <p>
                            <a href="#" class="underline">Manage your Privacy & Data Settings</a>
                        </p>
                    </div>
                </div>


            </x-side-bar>
        </x-slot:menu>
        {{ $slot }}
    </x-layout>
    @livewireScripts

    <script>
        // Global loader control functions
        function showGlobalLoader() {
            const loader = document.getElementById('global-loader');
            if (loader) {
                loader.style.display = 'flex';
            }
        }

        function hideGlobalLoader() {
            const loader = document.getElementById('global-loader');
            if (loader) {
                loader.style.display = 'none';
            }
        }

        // Make functions globally available
        window.showGlobalLoader = showGlobalLoader;
        window.hideGlobalLoader = hideGlobalLoader;

        // Test function - can be called from console
        window.testLoader = function() {
            showGlobalLoader();
            setTimeout(() => {
                hideGlobalLoader();
            }, 3000);
        };

        console.log('Global loader functions loaded. Test with: window.testLoader()');

        // Listen for Livewire events to control the global loader
        document.addEventListener('livewire:initialized', () => {
            console.log('Livewire initialized - Global loader listeners added');

            // Listen for specific Livewire method calls
            Livewire.hook('morph.updating', (el, toEl, childrenOnly, skip) => {
                // Check if this is a quote request by looking for loading states
                if (document.querySelector('[wire\\:loading][wire\\:target="getQuote"]')) {
                    showGlobalLoader();
                }
            });

            Livewire.hook('morph.updated', (el, toEl, childrenOnly, skip) => {
                hideGlobalLoader();
            });

            // Alternative approach: Listen for specific component events
            Livewire.on('loading-start', () => {
                showGlobalLoader();
            });

            Livewire.on('loading-end', () => {
                hideGlobalLoader();
            });
        });

        // Fallback: Monitor for wire:loading elements
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const target = mutation.target;
                    if (target.hasAttribute('wire:loading') && target.hasAttribute('wire:target')) {
                        const wireTarget = target.getAttribute('wire:target');
                        if (wireTarget === 'getQuote') {
                            if (target.style.display !== 'none' && !target.classList.contains('hidden')) {
                                showGlobalLoader();
                            } else {
                                hideGlobalLoader();
                            }
                        }
                    }
                }
            });
        });

        // Start observing when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            observer.observe(document.body, {
                attributes: true,
                subtree: true,
                attributeFilter: ['class', 'style']
            });
        });
    </script>
</body>

</html>
