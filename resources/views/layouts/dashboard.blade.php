<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="tallstackui_darkTheme()">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DoorTag - Shippers</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <tallstackui:script />
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased" x-cloak x-data="{ name: @js(auth()->user()->name) }" x-on:name-updated.window="name = $event.detail.name"
    x-bind:class="{ 'dark bg-gray-800': darkTheme, 'bg-gray-100': !darkTheme }">
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
                        <a href="{{ route('shipping.index') }}" @class([
                            'group h-[70px] flex items-center gap-4 px-4 rounded-md cursor-pointer transition hover:bg-white dark:hover:bg-gray-700 bg-transparent relative',
                            'bg-white dark:bg-gray-700' => request()->routeIs('shipping.index'),
                        ])>
                            <div class="w-[70px] h-[70px]">
                                <img src="{{ asset('assets/icons/menu-ship.png') }}" alt="Ship"
                                    class="w-full h-full object-contain" />
                            </div>
                            <span class="text-lg font-bold">Ship</span>
                        </a>

                        <a href="#" @class([
                            'group h-[70px] flex items-center gap-4 px-4 rounded-md cursor-pointer transition hover:bg-white dark:hover:bg-gray-700 bg-transparent relative',
                            'bg-white dark:bg-gray-700' => false,
                        ])>
                            <div class="w-[70px] h-[70px]">
                                <img src="{{ asset('assets/icons/menu-rates.png') }}" alt="Rates"
                                    class="w-full h-full object-contain" />
                            </div>
                            <div
                                class="absolute inset-0 transition-opacity hover:opacity-0 bg-gray-100/50 dark:bg-gray-800/50">
                            </div>
                            <span class="text-lg font-bold">Rates</span>
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
</body>

</html>
