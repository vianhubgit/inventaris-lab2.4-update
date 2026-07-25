<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — {{ config('app.name') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Inventaris">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    <script>
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full items-center justify-center bg-gradient-to-br from-brand-600 to-brand-900 p-4 antialiased dark:from-gray-900 dark:to-gray-800">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center text-white">
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo Inventaris TKJ" class="mx-auto mb-3 h-16 w-16 rounded-2xl object-cover shadow-lg ring-2 ring-white/30">
            <h1 class="text-2xl font-bold">SARANA & PRASARANA TKJ</h1>
            <p class="text-sm text-white/80">Lab A &bull; Lab B &bull; TEFA</p>
        </div>

        <div class="card p-6 sm:p-8">
            <h2 class="mb-1 text-xl font-bold">Selamat Datang</h2>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Masuk menggunakan username dan kata sandi Anda.</p>

            @include('layouts.partials.flash')

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4" data-no-loader>
                @csrf

                <div>
                    <label for="username" class="form-label">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}"
                           autofocus autocomplete="username" required
                           class="form-input @error('username') border-red-500 @enderror"
                           placeholder="contoh: admin">
                    @error('username')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                           class="form-input @error('password') border-red-500 @enderror"
                           placeholder="••••••••">
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    Ingat saya
                </label>

                <button type="submit" class="btn-primary w-full">Masuk</button>
            </form>
        </div>

        <button type="button" data-pwa-install
                class="mt-4 flex w-full items-center justify-center gap-2 rounded-lg border border-white/40 bg-white/10 px-4 py-2.5 text-sm font-medium text-white backdrop-blur transition hover:bg-white/20">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Instal / Download Aplikasi
        </button>

        <p class="mt-6 text-center text-xs text-white/70">
            &copy; {{ date('Y') }} Teknisi TKJ.V 0.21
        </p>
    </div>
</body>
</html>
