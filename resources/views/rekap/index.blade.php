<x-app-layout>
    <x-slot name="title">Rekap Okupansi</x-slot>

    <div class="space-y-4">

        {{-- Filter --}}
        <form method="GET" class="flex gap-2 items-end">
            <div class="flex-1">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Bulan</label>
                <input type="month" name="bulan" value="{{ $bulan }}"
                       class="mt-1 w-full h-11 px-4 text-sm bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            </div>
            <button type="submit"
                    class="h-11 px-5 bg-indigo-600 text-white text-sm font-bold rounded-2xl active:scale-[.98] transition-transform">
                Tampilkan
            </button>
        </form>

        @if($trips->isEmpty())
            <div class="bg-slate-50 rounded-3xl p-8 text-center border-2 border-dashed border-slate-200">
                <p class="text-slate-400 text-sm font-medium">Tidak ada trip pada bulan ini.</p>
            </div>
        @else
            @php
                $totalFilled   = $trips->sum('filled');
                $totalCapacity = $trips->sum('capacity');
                $avgPct        = $totalCapacity > 0 ? round($totalFilled / $totalCapacity * 100) : 0;
            @endphp

            {{-- Summary --}}
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl p-4 text-white">
                <p class="text-white/70 text-xs font-medium mb-3">Ringkasan Bulan Ini</p>
                <div class="grid grid-cols-3 gap-2">
                    <div class="bg-white/15 rounded-2xl p-3 text-center">
                        <p class="text-2xl font-bold">{{ $trips->count() }}</p>
                        <p class="text-white/70 text-[10px] font-medium mt-0.5">Trip</p>
                    </div>
                    <div class="bg-white/15 rounded-2xl p-3 text-center">
                        <p class="text-2xl font-bold">{{ $totalFilled }}</p>
                        <p class="text-white/70 text-[10px] font-medium mt-0.5">Penumpang</p>
                    </div>
                    <div class="bg-white/15 rounded-2xl p-3 text-center">
                        <p class="text-2xl font-bold">{{ $avgPct }}%</p>
                        <p class="text-white/70 text-[10px] font-medium mt-0.5">Rata-rata</p>
                    </div>
                </div>
            </div>

            {{-- Trip list --}}
            <div class="space-y-3">
                @foreach($trips as $item)
                    @php $trip = $item['trip']; @endphp
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
                         x-data="{ open: false }">

                        {{-- Trip header --}}
                        <button type="button" @click="open = !open"
                                class="w-full p-4 text-left active:bg-slate-50 transition-colors">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="text-xs text-slate-400 font-mono">{{ $trip->tanggal_berangkat->format('d/m/Y') }}</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $trip->schedule->label }}</p>
                                    <p class="text-xs text-slate-400">{{ $trip->schedule->route->name }} · {{ $trip->bus->nomor_lambung }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                        {{ $trip->status === 'dibuka' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                                        {{ $trip->status }}
                                    </span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                         :class="open ? 'rotate-180' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500
                                        {{ $item['pct'] >= 80 ? 'bg-emerald-500' : ($item['pct'] >= 50 ? 'bg-amber-400' : 'bg-indigo-400') }}"
                                         style="width: {{ $item['pct'] }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-600 shrink-0">
                                    {{ $item['filled'] }}/{{ $item['capacity'] }} ({{ $item['pct'] }}%)
                                </span>
                            </div>
                        </button>

                        {{-- Passenger list (expandable) --}}
                        <div x-show="open" x-collapse
                             class="border-t border-slate-100">
                            @if($trip->passengers->isEmpty())
                                <p class="text-xs text-slate-400 text-center py-4">Belum ada penumpang.</p>
                            @else
                                <div class="divide-y divide-slate-50">
                                    @foreach($trip->passengers->sortBy(fn($p) => $p->seat->nomor_kursi) as $p)
                                        <div class="flex items-center gap-3 px-4 py-3">
                                            {{-- Seat badge --}}
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 text-xs font-bold
                                                {{ $p->seat->kategori === 'sleeper' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                                {{ $p->seat->nomor_kursi }}
                                            </div>
                                            {{-- Passenger info --}}
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $p->nama_penumpang }}</p>
                                                <p class="text-xs text-slate-400 truncate">
                                                    {{ $p->no_hp ?: '—' }}
                                                    @if($p->alamat_naik) · {{ $p->alamat_naik }}@endif
                                                    @if($p->alamat_turun) → {{ $p->alamat_turun }}@endif
                                                </p>
                                            </div>
                                            {{-- Registered by --}}
                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] font-bold text-indigo-600">
                                                    {{ strtoupper(substr($p->inputBy?->name ?? '?', 0, 1)) }}
                                                </div>
                                                <span class="text-xs font-medium text-indigo-600 max-w-[60px] truncate">
                                                    {{ $p->inputBy?->name ?? '—' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
