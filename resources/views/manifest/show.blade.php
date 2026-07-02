<x-app-layout>
    <x-slot name="title">Manifest</x-slot>

    <div class="card p-4 mb-4">
        <p class="text-[11px] text-zinc-400">{{ $trip->schedule->route->name }}</p>
        <p class="text-sm font-semibold text-zinc-900">{{ $trip->schedule->label }}</p>
        <p class="text-[11px] text-zinc-400 mt-0.5">{{ $trip->tanggal_berangkat->format('d F Y') }} · {{ $trip->bus->nomor_lambung }}</p>
        <div class="flex gap-2 mt-3">
            <a href="{{ route('trips.seatmap', $trip) }}" class="btn-outline btn-sm flex-1 justify-center">← Denah</a>
            <a href="{{ route('manifest.pdf', $trip) }}" target="_blank" class="btn-default btn-sm flex-1 justify-center">Export PDF</a>
        </div>
    </div>

    <form method="GET" class="flex gap-2 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama penumpang..."
               class="input flex-1">
        @if(request('search'))
            <a href="{{ route('manifest.show', $trip) }}" class="btn-ghost btn-sm">Reset</a>
        @endif
    </form>

    <div class="card divide-y divide-zinc-100">
        @forelse($passengers as $p)
            <div class="flex items-center gap-3 px-4 py-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-xs font-semibold
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
                <p class="text-[11px] text-zinc-400 shrink-0">{{ $p->inputBy?->name }}</p>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-zinc-400">
                {{ request('search') ? 'Tidak ditemukan.' : 'Belum ada penumpang.' }}
            </div>
        @endforelse
    </div>
    <p class="mt-2 text-center text-[11px] text-zinc-400">{{ $passengers->count() }} penumpang</p>

</x-app-layout>
