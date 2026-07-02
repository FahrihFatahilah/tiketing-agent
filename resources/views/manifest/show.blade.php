<x-app-layout>
    <x-slot name="title">Manifest</x-slot>

    <div class="space-y-4">

        {{-- Trip Info --}}
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl p-4 text-white">
            <p class="text-white/70 text-xs">{{ $trip->schedule->route->name }}</p>
            <p class="font-bold text-base">{{ $trip->schedule->label }}</p>
            <p class="text-white/70 text-xs mt-0.5">{{ $trip->tanggal_berangkat->format('d F Y') }} · {{ $trip->bus->nomor_lambung }}</p>
            <div class="flex gap-2 mt-3">
                <a href="{{ route('trips.seatmap', $trip) }}"
                   class="flex-1 h-9 bg-white/20 text-white text-xs font-semibold rounded-xl flex items-center justify-center gap-1.5 active:bg-white/30">
                    ← Denah
                </a>
                <a href="{{ route('manifest.pdf', $trip) }}" target="_blank"
                   class="flex-1 h-9 bg-white text-indigo-600 text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 active:bg-white/90">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" class="flex gap-2">
            <div class="flex-1 relative">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama penumpang..."
                       class="w-full h-11 pl-10 pr-4 text-sm bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            </div>
            @if(request('search'))
                <a href="{{ route('manifest.show', $trip) }}"
                   class="h-11 px-4 bg-slate-100 text-slate-600 text-sm font-semibold rounded-2xl flex items-center active:bg-slate-200">
                    Reset
                </a>
            @endif
        </form>

        {{-- Passenger List --}}
        <div class="space-y-2">
            @forelse($passengers as $p)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 text-sm font-bold
                                {{ $p->seat->kategori === 'sleeper' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $p->seat->nomor_kursi }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ $p->nama_penumpang }}</p>
                        <p class="text-xs text-slate-400 truncate">
                            {{ $p->no_hp ?: '—' }}
                            @if($p->alamat_naik) · {{ $p->alamat_naik }} @endif
                            @if($p->alamat_turun) → {{ $p->alamat_turun }} @endif
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[10px] text-slate-400">{{ $p->inputBy->name }}</p>
                    </div>
                </div>
            @empty
                <div class="bg-slate-50 rounded-3xl p-8 text-center border-2 border-dashed border-slate-200">
                    <p class="text-slate-400 text-sm font-medium">
                        {{ request('search') ? 'Tidak ditemukan.' : 'Belum ada penumpang.' }}
                    </p>
                </div>
            @endforelse
        </div>

        <p class="text-xs text-slate-400 text-center pb-2">Total {{ $passengers->count() }} penumpang</p>
    </div>
</x-app-layout>
