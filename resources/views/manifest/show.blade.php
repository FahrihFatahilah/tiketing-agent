<x-app-layout>
    <x-slot name="title">Manifest Penumpang</x-slot>

    <div class="space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-sm font-semibold text-slate-900">{{ $trip->schedule->route->name }} — {{ $trip->schedule->label }}</p>
                <p class="text-xs text-slate-500">{{ $trip->tanggal_berangkat->format('d F Y') }} · {{ $trip->bus->nomor_lambung }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('trips.seatmap', $trip) }}">
                    <x-button variant="outline" size="sm">← Denah</x-button>
                </a>
                <a href="{{ route('manifest.pdf', $trip) }}" target="_blank">
                    <x-button size="sm">Export PDF</x-button>
                </a>
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" class="flex gap-2">
            <x-input name="search" value="{{ request('search') }}" placeholder="Cari nama penumpang..." class="w-64" />
            <x-button type="submit" variant="outline">Cari</x-button>
            @if(request('search'))
                <a href="{{ route('manifest.show', $trip) }}"><x-button variant="ghost">Reset</x-button></a>
            @endif
        </form>

        <x-card>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-12">Kursi</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">No. HP</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Naik</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Turun</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden xl:table-cell">Diinput oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($passengers as $p)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-md
                                             {{ $p->seat->kategori === 'sleeper' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}
                                             text-xs font-semibold">
                                    {{ $p->seat->nomor_kursi }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $p->nama_penumpang }}</td>
                            <td class="px-4 py-3 text-slate-500 hidden md:table-cell">{{ $p->no_hp ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 hidden lg:table-cell">{{ $p->alamat_naik ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 hidden lg:table-cell">{{ $p->alamat_turun ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-400 text-xs hidden xl:table-cell">{{ $p->inputBy->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-sm">
                                {{ request('search') ? 'Tidak ada penumpang dengan nama tersebut.' : 'Belum ada penumpang.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        <p class="text-xs text-slate-400">Total: {{ $passengers->count() }} penumpang</p>
    </div>
</x-app-layout>
