<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#6366f1">
    <title>{{ $title ?? 'Dashboard' }} — Agent Bus</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        [x-cloak] { display: none !important; }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom, 0px); }

        /* Smooth page transitions */
        .page-enter { animation: fadeSlideUp 0.2s ease-out; }
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Bottom nav active indicator */
        .nav-pill { transition: all 0.2s cubic-bezier(.4,0,.2,1); }

        /* Gradient header */
        .header-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }

        /* Card hover lift */
        .card-lift { transition: transform 0.15s ease, box-shadow 0.15s ease; }
        .card-lift:hover { transform: translateY(-2px); box-shadow: 0 8px 25px -5px rgba(0,0,0,.12); }

        /* Scrollbar hide */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Sheet slide up */
        .sheet-enter { animation: sheetUp 0.25s cubic-bezier(.4,0,.2,1); }
        @keyframes sheetUp {
            from { transform: translateY(100%); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="h-full bg-slate-100 text-slate-900 antialiased">
<div x-data="{ sidebarOpen: false }" class="flex flex-col h-full min-h-screen max-w-md mx-auto bg-white shadow-2xl relative">

    {{-- ── TOP HEADER ── --}}
    <header class="header-gradient text-white shrink-0 safe-top">
        <div class="flex items-center justify-between px-4 pt-4 pb-3">
            <div class="flex items-center gap-3">
                {{-- Mobile menu (for admin extra pages) --}}
                @role('admin')
                <button @click="sidebarOpen = true"
                        class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center active:bg-white/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                @endrole
                <div>
                    <p class="text-xs text-white/70 font-medium">Agent Muliajaya</p>
                    <h1 class="text-base font-bold leading-tight">{{ $title ?? 'Dashboard' }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{-- User avatar --}}
                <a href="{{ route('profile.edit') }}"
                   class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold active:bg-white/30 transition-colors">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </a>
            </div>
        </div>
    </header>

    {{-- ── SLIDE-IN DRAWER (Admin only) ── --}}
    @role('admin')
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50 flex" style="display:none">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="sidebarOpen = false"></div>
        <div class="relative w-72 bg-white h-full flex flex-col shadow-2xl"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">

            <div class="header-gradient text-white px-5 py-6">
                <p class="text-xs text-white/70">Admin Panel</p>
                <p class="font-bold text-lg">Agent Muliajaya</p>
                <p class="text-xs text-white/70 mt-1">{{ auth()->user()->email }}</p>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pb-1">Master Data</p>
                @php
                    $drawerLinks = [
                        ['route' => 'admin.schedules.index', 'label' => 'Jadwal', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['route' => 'admin.routes.index',    'label' => 'Rute',   'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],
                        ['route' => 'admin.buses.index',     'label' => 'Armada', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                        ['route' => 'admin.users.index',     'label' => 'Pengguna','icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'],
                    ];
                @endphp
                @foreach($drawerLinks as $link)
                <a href="{{ route($link['route']) }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs($link['route'].'*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $link['icon'] }}"/>
                    </svg>
                    {{ $link['label'] }}
                </a>
                @endforeach
            </nav>

            <div class="border-t border-slate-100 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endrole

    {{-- ── MAIN CONTENT ── --}}
    <main class="flex-1 overflow-y-auto no-scrollbar pb-20">
        {{-- Toast notifications --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-cloak
                 x-init="setTimeout(() => show = false, 3500)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mx-4 mt-3 flex items-center gap-2.5 bg-emerald-500 text-white text-sm px-4 py-3 rounded-2xl shadow-lg">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mx-4 mt-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-2xl">
                @foreach($errors->all() as $error)
                    <p class="flex items-center gap-1.5"><span class="text-red-400">•</span> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="page-enter px-4 py-4">
            {{ $slot }}
        </div>
    </main>

    {{-- ── BOTTOM NAVIGATION ── --}}
    <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-white border-t border-slate-100 safe-bottom z-40 shadow-[0_-4px_20px_rgba(0,0,0,.06)]">
        <div class="flex items-center justify-around px-2 py-2">
            @php
                $navItems = [
                    ['route' => 'dashboard',   'label' => 'Home',    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'trips.index', 'label' => 'Jadwal',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['route' => 'rekap.index', 'label' => 'Rekap',   'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'roles' => ['admin','pengurus']],
                    ['route' => 'profile.edit','label' => 'Profil',  'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ];
            @endphp

            @foreach($navItems as $item)
                @if(!isset($item['roles']) || auth()->user()->hasAnyRole($item['roles']))
                    @php $isActive = request()->routeIs($item['route'].'*') || request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="nav-pill flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-2xl min-w-[56px]
                              {{ $isActive ? 'bg-indigo-50' : '' }}">
                        <svg class="w-5 h-5 transition-colors {{ $isActive ? 'text-indigo-600' : 'text-slate-400' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isActive ? '2' : '1.5' }}"
                                  d="{{ $item['icon'] }}"/>
                        </svg>
                        <span class="text-[10px] font-semibold {{ $isActive ? 'text-indigo-600' : 'text-slate-400' }}">
                            {{ $item['label'] }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </nav>

</div>
</body>
</html>
