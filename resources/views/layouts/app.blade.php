<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    {{-- Progressive Web App (bisa dipasang ke layar utama HP & desktop) --}}
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Inventaris">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    {{-- Cegah flash dark mode sebelum JS termuat --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-100 text-gray-900 antialiased dark:bg-gray-900 dark:text-gray-100">
    <div id="global-loader"></div>

    @php($user = auth()->user())

    <div class="min-h-full">
        {{-- Backdrop mobile --}}
        <div data-sidebar-backdrop class="fixed inset-0 z-30 hidden bg-black/50 lg:hidden"></div>

        {{-- Sidebar --}}
        <aside data-sidebar
               class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full transform overflow-y-auto border-r border-gray-200 bg-white transition-transform duration-200 dark:border-gray-700 dark:bg-gray-800 lg:translate-x-0">
            <div class="flex h-16 items-center gap-2 border-b border-gray-200 px-5 dark:border-gray-700">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo Inventaris TKJ" class="h-9 w-9 rounded-lg object-cover">
                <div class="leading-tight">
                    <p class="text-sm font-bold">Inventaris</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">TKJ</p>
                </div>
            </div>

            <nav class="space-y-1 p-4">
                @if($user->isAdmin())
                    @include('layouts.partials.sidebar-admin')
                @else
                    @include('layouts.partials.sidebar-sekretaris')
                @endif
            </nav>
        </aside>

        {{-- Konten utama --}}
        <div class="lg:pl-64">
            @include('layouts.partials.topbar')

            <main class="p-4 sm:p-6 lg:p-8">
                @include('layouts.partials.flash')

                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">@hasSection('page-title')@yield('page-title')@else@yield('title')@endif</h1>
                        @hasSection('breadcrumb')
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@yield('breadcrumb')</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">@yield('actions')</div>
                </div>

                @yield('content')
            </main>

            <footer class="px-6 py-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Inventaris Laboratorium TKJ — Lab A, Lab B, TEFA.
            </footer>
        </div>
    </div>
</body>
</html>
