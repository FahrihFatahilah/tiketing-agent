<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Date + action --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-zinc-500">{{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}</p>
        <a href="{{ route('trips.index') }}" class="btn-default btn-sm">+ Jadwal Baru</a>
    </div>

    {{-- Today trips --}}
    <div class="space-y-2">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-zinc-400">Trip Hari Ini</h2>

        @if($trips->isEmpty())
            <div class="card flex flex-col items-center justify-center gap-2 py-10 text-center">
                <svg class="h-8 w-8 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <p class="text-sm text-zinc-500">Belum ada trip hari ini</p>
                <a href="{{ route('trips.index') }}" class="text-xs font-medium text-zinc-900 underline underline-offset-2">Buka sekarang</a>
            </div>
        @else
            @foreach($trips as $item)
                @php
                    $trip = $item['trip'];
                    $pct  = $item['capacity'] > 0 ? round($item['filled'] / $item['capacity'] * 100) : 0;
                @endphp
                <a href="{{ route('trips.seatmap', $trip) }}" class="card block p-4 hover:bg-zinc-50 transition-colors active:bg-zinc-100">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-[11px] text-zinc-400 font-medium">{{ $trip->schedule->route->name }}</p>
                            <p class="text-sm font-semibold text-zinc-900 mt-0.5">{{ $trip->schedule->label }}</p>
                            <p class="text-[11px] text-zinc-400 mt-0.5">{{ $trip->bus->nomor_lambung }} · {{ $trip->bus->busType->name }}</p>
                        </div>
                        <span class="{{ $trip->status === 'dibuka' ? 'badge-success' : 'badge-destructive' }} badge">
                            {{ $trip->status }}
                        </span>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-[11px] text-zinc-500">
                            <span>{{ $item['filled'] }} / {{ $item['capacity'] }} kursi</span>
                            <span class="font-medium text-zinc-700">{{ $pct }}%</span>
                        </div>
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-zinc-100">
                            <div class="h-full rounded-full bg-zinc-900 transition-all duration-500" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                </a>
            @endforeach
        @endif
    </div>

    {{-- Schedules --}}
    <div class="space-y-2">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-zinc-400">Jadwal Tetap</h2>
        <div class="card divide-y divide-zinc-100">
            @foreach($schedules as $schedule)
                <div class="flex items-center justify-between px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-zinc-900">{{ $schedule->label }}</p>
                        <p class="text-[11px] text-zinc-400">{{ $schedule->route->name }}</p>
                    </div>
                    <span class="font-mono text-sm font-medium text-zinc-700">
                        {{ \Carbon\Carbon::parse($schedule->jam_berangkat)->format('H:i') }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

</x-app-layout>
