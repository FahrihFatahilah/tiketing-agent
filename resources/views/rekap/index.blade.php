<x-app-layout>
    <x-slot name="title">Rekap Okupansi</x-slot>

    <div class="space-y-5">
        {{-- Filter --}}
        <form method="GET" class="flex items-end gap-3">
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Bulan</label>
                <input type="month" name="bulan" value="{{ $bulan }}"
                       class="h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
            </div>
            <x-button type="submit" variant="outline">Tampilkan</x-button>
        </form>

        @if($trips->isEmpty())
            <x-card class="p-8 text-center">
                <p class="text-slate-400 text-sm">Tidak ada trip pada bulan ini.</p>
            </x-card>
        @else
            @php
                $totalFilled = $trips->sum('filled');
                $totalCapacity = $trips->sum('capacity');
                $avgPct = $totalCapacity > 0 ? round($totalFilled / $totalCapacity * 100) : 0;
            @endphp

            {{-- Summary --}}
            <div class="grid grid-cols-3 gap-4">
                <x-card class="px-4 py-3">
                    <p class="text-xs text-slate-500">Total Trip</p>
                    <p class="text-2xl font-semibold text-slate-900 mt-0.5">{{ $trips->count() }}</p>
                </x-card>
                <x-card class="px-4 py-3">
                    <p class="text-xs text-slate-500">Total Penumpang</p>
                    <p class="text-2xl font-semibold text-slate-900 mt-0.5">{{ $totalFilled }}</p>
                </x-card>
                <x-card class="px-4 py-3">
                    <p class="text-xs text-slate-500">Rata-rata Okupansi</p>
                    <p class="text-2xl font-semibold text-slate-900 mt-0.5">{{ $avgPct }}%</p>
                </x-card>
            </div>

            <x-card>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tanggal</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Jadwal</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Armada</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-48">Okupansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($trips as $item)
                            @php $trip = $item['trip']; @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-mono text-slate-600">{{ $trip->tanggal_berangkat->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-900">{{ $trip->schedule->label }}</p>
                                    <p class="text-xs text-slate-400">{{ $trip->schedule->route->name }}</p>
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-600">{{ $trip->bus->nomor_lambung }}</td>
                                <td class="px-4 py-3">
                                    <x-badge variant="{{ $trip->status === 'dibuka' ? 'success' : 'danger' }}">
                                        {{ $trip->status }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $item['pct'] >= 80 ? 'bg-emerald-500' : ($item['pct'] >= 50 ? 'bg-amber-400' : 'bg-slate-300') }}"
                                                 style="width: {{ $item['pct'] }}%"></div>
                                        </div>
                                        <span class="text-xs text-slate-500 w-16 shrink-0">{{ $item['filled'] }}/{{ $item['capacity'] }} ({{ $item['pct'] }}%)</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-card>
        @endif
    </div>
</x-app-layout>
