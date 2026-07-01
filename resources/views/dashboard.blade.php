<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</p>
            </div>
            <a href="{{ route('trips.index') }}">
                <x-button>+ Buka Jadwal Baru</x-button>
            </a>
        </div>

        {{-- Today's trips --}}
        @if($trips->isEmpty())
            <x-card class="p-8 text-center">
                <p class="text-slate-400 text-sm">Belum ada trip yang dibuka untuk hari ini.</p>
                <a href="{{ route('trips.index') }}" class="mt-3 inline-block text-sm text-slate-900 font-medium underline underline-offset-2">Buka jadwal sekarang</a>
            </x-card>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach($trips as $item)
                    @php $trip = $item['trip']; @endphp
                    <x-card class="p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-xs text-slate-500 font-medium">{{ $trip->schedule->route->name }}</p>
                                <p class="text-lg font-semibold text-slate-900 mt-0.5">{{ $trip->schedule->label }}</p>
                            </div>
                            <x-badge variant="{{ $trip->status === 'dibuka' ? 'success' : 'danger' }}">
                                {{ $trip->status }}
                            </x-badge>
                        </div>

                        {{-- Occupancy bar --}}
                        <div class="mb-3">
                            <div class="flex justify-between text-xs text-slate-500 mb-1">
                                <span>{{ $item['filled'] }} terisi</span>
                                <span>{{ $item['capacity'] }} kursi</span>
                            </div>
                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full transition-all"
                                     style="width: {{ $item['capacity'] > 0 ? round($item['filled'] / $item['capacity'] * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <p class="text-xs text-slate-400">{{ $trip->bus->nomor_lambung }} · {{ $trip->bus->busType->name }}</p>
                            <a href="{{ route('trips.seatmap', $trip) }}">
                                <x-button variant="outline" size="sm">Denah</x-button>
                            </a>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif

        {{-- All schedules reference --}}
        <x-card>
            <div class="px-4 py-3 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-900">Jadwal Tetap</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($schedules as $schedule)
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $schedule->label }}</p>
                            <p class="text-xs text-slate-500">{{ $schedule->route->name }}</p>
                        </div>
                        <span class="text-sm font-mono text-slate-600">{{ \Carbon\Carbon::parse($schedule->jam_berangkat)->format('H:i') }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
</x-app-layout>
