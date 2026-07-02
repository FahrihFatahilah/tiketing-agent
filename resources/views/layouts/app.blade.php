<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Agent Bus</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=geist:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Geist', system-ui, sans-serif; }
        [x-cloak] { display: none !important; }
        .safe-bottom { padding-bottom: max(env(safe-area-inset-bottom, 0px), 0px); }
    </style>
</head>
<body class="h-full bg-zinc-50 antialiased">

<div x-data="{ drawerOpen: false }" class="relative flex flex-col min-h-screen max-w-md mx-auto bg-white shadow-[0_0_0_1px_rgba(0,0,0,.05),0_4px_24px_rgba(0,0,0,.06)]">

    {{-- Top bar --}}
    <header class="sticky top-0 z-30 flex h-14 items-center gap-3 border-b border-zinc-200 bg-white/95 backdrop-blur px-4">
        @role('admin')
        <button @click="drawerOpen = true" class="btn-ghost btn-icon rounded-md -ml-1">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        @endrole
        <span class="flex-1 text-sm font-semibold tracking-tight truncate">{{ $title ?? 'Dashboard' }}</span>
        <a href="{{ route('profile.edit') }}"
           class="flex h-7 w-7 items-center justify-center rounded-full bg-zinc-900 text-xs font-semibold text-white">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </a>
    </header>

    {{-- Admin drawer --}}
    @role('admin')
    <div x-show="drawerOpen" x-cloak class="fixed inset-0 z-50 flex" style="display:none">
        <div class="absolute inset-0 bg-black/40" @click="drawerOpen = false"></div>
        <aside class="relative z-10 flex w-64 flex-col bg-white border-r border-zinc-200 shadow-xl"
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-150"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full">

            <div class="flex h-14 items-center border-b border-zinc-200 px-4">
                <span class="text-sm font-semibold">Agent Muliajaya</span>
            </div>

            <nav class="flex-1 overflow-y-auto p-2 space-y-0.5">
                <p class="px-2 py-1.5 text-[11px] font-semibold uppercase tracking-widest text-zinc-400">Master Data</p>
                @php
                    $links = [
                        ['route' => 'admin.schedules.index', 'label' => 'Jadwal',   'pattern' => 'admin.schedules.*'],
                        ['route' => 'admin.routes.index',    'label' => 'Rute',     'pattern' => 'admin.routes.*'],
                        ['route' => 'admin.buses.index',     'label' => 'Armada',   'pattern' => 'admin.buses.*'],
                        ['route' => 'admin.users.index',     'label' => 'Pengguna', 'pattern' => 'admin.users.*'],
                    ];
                @endphp
                @foreach($links as $link)
                    <a href="{{ route($link['route']) }}" @click="drawerOpen = false"
                       class="flex items-center rounded-md px-2 py-1.5 text-sm transition-colors
                              {{ request()->routeIs($link['pattern']) ? 'bg-zinc-100 font-medium text-zinc-900' : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-zinc-200 p-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center rounded-md px-2 py-1.5 text-sm text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 transition-colors">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>
    </div>
    @endrole

    {{-- Main --}}
    <main class="flex-1 overflow-y-auto">
        {{-- Toast --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-cloak
                 x-init="setTimeout(() => show = false, 3500)"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-end="opacity-0"
                 class="mx-4 mt-3 flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-4 py-3 text-sm shadow-md">
                <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-zinc-700">{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mx-4 mt-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="px-4 py-4 space-y-4">
            {{ $slot }}
        </div>
    </main>

    {{-- Bottom nav --}}
    <nav class="sticky bottom-0 z-30 border-t border-zinc-200 bg-white/95 backdrop-blur safe-bottom">
        <div class="flex items-center">
            @php
                $navItems = [
                    ['route' => 'dashboard',    'label' => 'Beranda',
                     'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'trips.index',  'label' => 'Jadwal',
                     'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['route' => 'rekap.index',  'label' => 'Rekap',
                     'icon'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                     'roles' => ['admin','pengurus']],
                    ['route' => 'profile.edit', 'label' => 'Profil',
                     'icon'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ];
            @endphp
            @foreach($navItems as $item)
                @if(!isset($item['roles']) || auth()->user()->hasAnyRole($item['roles']))
                    @php $active = request()->routeIs($item['route']) || request()->routeIs($item['route'].'*'); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex flex-1 flex-col items-center gap-1 py-3 text-[11px] font-medium transition-colors
                              {{ $active ? 'text-zinc-900' : 'text-zinc-400 hover:text-zinc-600' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="{{ $active ? '2' : '1.5' }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                        </svg>
                        {{ $item['label'] }}
                        @if($active)
                            <span class="absolute bottom-0 h-0.5 w-8 rounded-full bg-zinc-900" style="position:relative; margin-top:-4px;"></span>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>
    </nav>

</div>
</body>
</html>
