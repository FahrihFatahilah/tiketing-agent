<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="space-y-5">

        {{-- Greeting --}}
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl p-5 text-white">
            <p class="text-white/70 text-xs font-medium">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</p>
            <p class="text-xl font-bold mt-1">Halo, {{ explode(' ', auth()->user()->name)[0] }} 👋</p>
            <p class="text-white/70 text-sm mt-0.5">{{ $trips->count() }} trip aktif hari ini</p>
            <a href="{{ route('trips.index') }}"
               class="mt-4 inline-flex items-center gap-2 bg-white text-indigo-600 text-sm font-semibold px-4 py-2 rounded-xl active:scale-95 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buka Jadwal Baru
            </a>
        </div>

        {{-- Today's trips --}}
        <div>
            <h2 class="text-sm font-bold text-slate-700 mb-3">Trip Hari Ini</h2>

            @if($trips->isEmpty())
                <div class="bg-slate-50 rounded-3xl p-8 text-center border-2 border-dashed border-slate-200">
                    <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <p class="text-slate-400 text-sm font-medium">Belum ada trip hari ini</p>
                    <a href="{{ route('trips.index') }}" class="mt-2 inline-block text-indigo-600 text-sm font-semibold">Buka sekarang →</a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($trips as $item)
                        @php
                            $trip = $item['trip'];
                            $pct  = $item['capacity'] > 0 ? round($item['filled'] / $item['capacity'] * 100) : 0;
                        @endphp
                        <a href="{{ route('trips.seatmap', $trip) }}"
                           class="card-lift block bg-white rounded-3xl p-4 shadow-sm border border-slate-100 active:scale-[.98] transition-transform">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-indigo-100 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 font-medium">{{ $trip->schedule->route->name }}</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $trip->schedule->label }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                    {{ $trip->status === 'dibuka' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                                    {{ $trip->status }}
                                </span>
                            </div>

                            {{-- Progress --}}
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-xs text-slate-500">
                                    <span>{{ $item['filled'] }} terisi</span>
                                    <span class="font-semibold text-slate-700">{{ $pct }}%</span>
                                </div>
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500
                                        {{ $pct >= 80 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-amber-400' : 'bg-indigo-400') }}"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                                <p class="text-xs text-slate-400">{{ $trip->bus->nomor_lambung }} · {{ $trip->bus->busType->name }} · {{ $item['capacity'] }} kursi</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Schedules --}}
        <div>
            <h2 class="text-sm font-bold text-slate-700 mb-3">Jadwal Tetap</h2>
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                @foreach($schedules as $i => $schedule)
                    <div class="flex items-center justify-between px-4 py-3.5 {{ $i > 0 ? 'border-t border-slate-50' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-purple-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $schedule->label }}</p>
                                <p class="text-xs text-slate-400">{{ $schedule->route->name }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold font-mono text-indigo-600">
                            {{ \Carbon\Carbon::parse($schedule->jam_berangkat)->format('H:i') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</x-app-layout>
