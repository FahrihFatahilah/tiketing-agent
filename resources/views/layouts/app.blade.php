<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — {{ $title ?? 'Dashboard' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --radius: 0.5rem;
            --border: #e2e8f0;
            --muted: #f8fafc;
            --muted-fg: #64748b;
            --accent: #0f172a;
        }
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-900 antialiased">
<div x-data="{ sidebarOpen: false }" class="flex h-full min-h-screen">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black/40 lg:hidden" x-cloak></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 w-60 bg-white border-r border-slate-200 flex flex-col transition-transform duration-200 lg:relative lg:translate-x-0 lg:flex">
        <div class="h-14 flex items-center px-5 border-b border-slate-200">
            <span class="font-semibold text-slate-900 tracking-tight">Agent Muliajaya</span>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                <x-icon-home /> Dashboard
            </x-nav-link>
            <x-nav-link href="{{ route('trips.index') }}" :active="request()->routeIs('trips.*')">
                <x-icon-map /> Pilih Jadwal
            </x-nav-link>

            @role('admin|pengurus')
            <div class="pt-3 pb-1 px-2">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Laporan</p>
            </div>
            <x-nav-link href="{{ route('rekap.index') }}" :active="request()->routeIs('rekap.*')">
                <x-icon-bar-chart /> Rekap Okupansi
            </x-nav-link>
            @endrole

            @role('admin')
            <div class="pt-3 pb-1 px-2">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Master Data</p>
            </div>
            <x-nav-link href="{{ route('admin.schedules.index') }}" :active="request()->routeIs('admin.schedules.*')">
                <x-icon-clock /> Jadwal
            </x-nav-link>
            <x-nav-link href="{{ route('admin.routes.index') }}" :active="request()->routeIs('admin.routes.*')">
                <x-icon-map /> Rute
            </x-nav-link>
            <x-nav-link href="{{ route('admin.buses.index') }}" :active="request()->routeIs('admin.buses.*')">
                <x-icon-bus /> Armada
            </x-nav-link>
            <x-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                <x-icon-users /> Pengguna
            </x-nav-link>
            @endrole
        </nav>

        <div class="border-t border-slate-200 p-3">
            <div class="flex items-center gap-2 px-2 py-2 rounded-md">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 capitalize">{{ auth()->user()->getRoleNames()->first() }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="w-full text-left text-sm text-slate-500 hover:text-slate-900 px-2 py-1.5 rounded-md hover:bg-slate-100 transition-colors">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0 lg:ml-0">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center px-4 shrink-0 gap-3">
            <button @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-md text-slate-500 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-sm font-semibold text-slate-900">{{ $title ?? 'Dashboard' }}</h1>
        </header>

        <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-4 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-lg">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
