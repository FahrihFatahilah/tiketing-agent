<x-app-layout>
    <x-slot name="title">Rekap Okupansi</x-slot>

    <div class="flex flex-wrap items-end gap-2 mb-4">
        <form method="GET" class="flex gap-2 flex-1">
            <input type="month" name="bulan" value="{{ $bulan }}" class="input flex-1">
            <button type="submit" class="btn-default btn-sm px-4">Tampilkan</button>
        </form>
        <a href="{{ route('rekap.pdf', ['bulan' => $bulan]) }}" class="btn-outline btn-sm px-4">Export PDF</a>
    </div>

    @if($trips->isEmpty())
        <div class="card flex flex-col items-center justify-center gap-2 py-10 text-center">
            <p class="text-sm text-zinc-500">Tidak ada trip pada bulan ini.</p>
        </div>
    @else
        @php
            $totalFilled   = $trips->sum('filled');
            $totalCapacity = $trips->sum('capacity');
            $avgPct        = $totalCapacity > 0 ? round($totalFilled / $totalCapacity * 100) : 0;
        @endphp

        {{-- Summary --}}
        <div class="grid grid-cols-3 gap-2 mb-4">
            <div class="card p-3 text-center">
                <p class="text-xl font-semibold text-zinc-900">{{ $trips->count() }}</p>
                <p class="text-[11px] text-zinc-400 mt-0.5">Trip</p>
            </div>
            <div class="card p-3 text-center">
                <p class="text-xl font-semibold text-zinc-900">{{ $totalFilled }}</p>
                <p class="text-[11px] text-zinc-400 mt-0.5">Penumpang</p>
            </div>
            <div class="card p-3 text-center">
                <p class="text-xl font-semibold text-zinc-900">{{ $avgPct }}%</p>
                <p class="text-[11px] text-zinc-400 mt-0.5">Rata-rata</p>
            </div>
        </div>

        {{-- Trip list --}}
        <div class="space-y-2">
            @foreach($trips as $item)
                @php $trip = $item['trip']; @endphp
                <div class="card overflow-hidden" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                            class="w-full px-4 py-3 text-left hover:bg-zinc-50 transition-colors">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="text-[11px] font-mono text-zinc-400">{{ $trip->tanggal_berangkat->format('d/m/Y') }}</p>
                                <p class="text-sm font-semibold text-zinc-900">{{ $trip->schedule->label }}</p>
                                <p class="text-[11px] text-zinc-400">{{ $trip->schedule->route->name }} · {{ $trip->bus->nomor_lambung }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="{{ $trip->status === 'dibuka' ? 'badge-success' : 'badge-destructive' }} badge">
                                    {{ $trip->status }}
                                </span>
                                <svg class="h-4 w-4 text-zinc-400 transition-transform duration-150"
                                     :class="open ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-zinc-100">
                                <div class="h-full rounded-full bg-zinc-900 transition-all" style="width:{{ $item['pct'] }}%"></div>
                            </div>
                            <span class="text-[11px] font-medium text-zinc-600 shrink-0">
                                {{ $item['filled'] }}/{{ $item['capacity'] }} ({{ $item['pct'] }}%)
                            </span>
                        </div>
                    </button>

                    <div x-show="open" x-collapse class="border-t border-zinc-100">
                        @if($trip->passengers->isEmpty())
                            <p class="py-4 text-center text-xs text-zinc-400">Belum ada penumpang.</p>
                        @else
                            <div class="divide-y divide-zinc-50">
                                @foreach($trip->passengers->sortBy(fn($p) => $p->seat->nomor_kursi) as $p)
                                    <div class="flex items-center gap-3 px-4 py-2.5">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-[11px] font-semibold
                                                    {{ $p->seat->kategori === 'sleeper' ? 'bg-zinc-100 text-zinc-600' : 'bg-zinc-900 text-white' }}">
                                            {{ $p->seat->nomor_kursi }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-zinc-900 truncate">{{ $p->nama_penumpang }}</p>
                                            <p class="text-[11px] text-zinc-400 truncate">
                                                {{ $p->no_hp ?: '—' }}
                                                @if($p->alamat_naik) · {{ $p->alamat_naik }}@endif
                                                @if($p->alamat_turun) → {{ $p->alamat_turun }}@endif
                                            </p>
                                        </div>
                                        <p class="text-[11px] text-zinc-400 shrink-0 max-w-[64px] truncate text-right">
                                            {{ $p->inputBy?->name ?? '—' }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</x-app-layout>
