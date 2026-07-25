<header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-gray-200 bg-white/80 px-4 backdrop-blur dark:border-gray-700 dark:bg-gray-800/80 sm:px-6">
    <button data-sidebar-toggle class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 lg:hidden" aria-label="Buka menu">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    <div class="hidden text-sm text-gray-500 dark:text-gray-400 lg:block">
        {{ now()->translatedFormat('l, d F Y') }}
    </div>

    <div class="flex items-center gap-3">
        {{-- Instal aplikasi (PWA) --}}
        <button type="button" data-pwa-install
                class="rounded-lg p-2 text-gray-500 hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-900/30"
                title="Instal / Download aplikasi" aria-label="Instal aplikasi">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </button>

        {{-- Notifikasi --}}
        @include('layouts.partials.notifications')

        {{-- Toggle dark mode --}}
        <button data-theme-toggle class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700" aria-label="Ganti tema">
            <svg class="h-5 w-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg class="hidden h-5 w-5 dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>

        {{-- User --}}
        <div class="flex items-center gap-3 border-l border-gray-200 pl-2 dark:border-gray-700 sm:pl-3">
            <div class="hidden text-right leading-tight sm:block">
                <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-xs capitalize text-gray-500 dark:text-gray-400">{{ auth()->user()->role->label() }}</p>
            </div>
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 font-semibold text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>
            <form method="POST" action="{{ route('logout') }}" data-confirm="Keluar dari sistem?">
                @csrf
                <button type="submit" class="rounded-lg p-2 text-gray-500 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/30" aria-label="Keluar">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</header>
