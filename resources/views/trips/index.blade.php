<x-app-layout>
    <x-slot name="title">Denah Kursi</x-slot>

    <div class="space-y-4" x-data="seatApp()">

        {{-- Trip Selector --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-4">
            <form method="POST" action="{{ route('trips.store') }}" class="space-y-3">
                @csrf

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Jadwal</label>
                    <select name="schedule_id" required
                            class="w-full h-11 px-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition appearance-none">
                        <option value="">Pilih jadwal...</option>
                        @foreach($schedules as $s)
                            <option value="{{ $s->id }}" {{ $trip && $trip->schedule_id == $s->id ? 'selected' : '' }}>
                                {{ $s->route->name }} — {{ $s->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Tanggal</label>
                        <input type="date" name="tanggal"
                               value="{{ $trip ? $trip->tanggal_berangkat->format('Y-m-d') : today()->format('Y-m-d') }}"
                               class="w-full h-11 px-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Armada</label>
                        <select name="bus_id" required
                                class="w-full h-11 px-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition appearance-none">
                            <option value="">Pilih...</option>
                            @foreach($buses as $b)
                                <option value="{{ $b->id }}" {{ $trip && $trip->bus_id == $b->id ? 'selected' : '' }}>
                                    {{ $b->nomor_lambung }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit"
                        class="w-full h-11 bg-indigo-600 text-white text-sm font-bold rounded-2xl active:scale-[.98] transition-transform hover:bg-indigo-700">
                    Tampilkan Denah
                </button>
            </form>
        </div>

        @if($trip && $seatMap)
            @php
                $capacity = $trip->bus->busType->total_seat;
                $filled   = $occupiedSeats->count();
                $empty    = $capacity - $filled;
                $pct      = $capacity > 0 ? round($filled / $capacity * 100) : 0;
            @endphp

            {{-- Trip Info Card --}}
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl p-4 text-white">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-white/70 text-xs">{{ $trip->schedule->route->name }}</p>
                        <p class="font-bold text-base">{{ $trip->schedule->label }}</p>
                        <p class="text-white/70 text-xs mt-0.5">{{ $trip->bus->nomor_lambung }} · {{ $trip->tanggal_berangkat->format('d M Y') }}</p>
                    </div>
                    <span class="text-xs font-bold px-3 py-1.5 rounded-full
                        {{ $trip->status === 'dibuka' ? 'bg-emerald-400/30 text-emerald-100' : 'bg-red-400/30 text-red-100' }}">
                        {{ ucfirst($trip->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2 mb-3">
                    <div class="bg-white/15 rounded-2xl p-2.5 text-center">
                        <p class="text-lg font-bold">{{ $filled }}</p>
                        <p class="text-white/70 text-[10px] font-medium">Terisi</p>
                    </div>
                    <div class="bg-white/15 rounded-2xl p-2.5 text-center">
                        <p class="text-lg font-bold">{{ $empty }}</p>
                        <p class="text-white/70 text-[10px] font-medium">Kosong</p>
                    </div>
                    <div class="bg-white/15 rounded-2xl p-2.5 text-center">
                        <p class="text-lg font-bold">{{ $pct }}%</p>
                        <p class="text-white/70 text-[10px] font-medium">Penuh</p>
                    </div>
                </div>

                <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-white rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
                </div>

                <div class="flex gap-2 mt-3">
                    <a href="{{ route('manifest.show', $trip) }}"
                       class="flex-1 h-9 bg-white/20 text-white text-xs font-semibold rounded-xl flex items-center justify-center gap-1.5 active:bg-white/30 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Manifest
                    </a>
                    @role('admin|pengurus')
                    <form action="{{ route('trips.status', $trip) }}" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $trip->status === 'dibuka' ? 'ditutup' : 'dibuka' }}">
                        <button type="submit"
                                onclick="return confirm('{{ $trip->status === 'dibuka' ? 'Tutup trip ini?' : 'Buka kembali trip ini?' }}')"
                                class="w-full h-9 text-xs font-semibold rounded-xl flex items-center justify-center gap-1.5 transition-colors
                                    {{ $trip->status === 'dibuka' ? 'bg-red-400/30 text-red-100 active:bg-red-400/50' : 'bg-emerald-400/30 text-emerald-100 active:bg-emerald-400/50' }}">
                            {{ $trip->status === 'dibuka' ? '🔒 Tutup Trip' : '🔓 Buka Kembali' }}
                        </button>
                    </form>
                    @endrole
                </div>
            </div>

            {{-- Seat Map --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="flex items-center gap-1.5">
                        <div class="w-4 h-4 rounded-md bg-emerald-500"></div>
                        <span class="text-xs text-slate-500">Terisi</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-4 h-4 rounded-md bg-slate-100 border border-slate-300"></div>
                        <span class="text-xs text-slate-500">Kosong</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-4 h-4 rounded-md bg-amber-100 border border-amber-300"></div>
                        <span class="text-xs text-slate-500">Sleeper</span>
                    </div>
                </div>

                <div class="flex justify-center">
                    <div class="w-full max-w-[260px]">
                        <div class="flex justify-center mb-4">
                            <div class="bg-indigo-50 border border-indigo-200 rounded-xl px-5 py-1.5 text-xs font-bold text-indigo-500 tracking-widest">
                                ▲ DEPAN
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-2 mb-3">
                            <div class="col-span-2"></div>
                            <div></div>
                            <div class="col-span-2 flex justify-end">
                                <div class="w-11 h-11 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke-width="1.5"/>
                                        <path stroke-linecap="round" stroke-width="1.5" d="M12 8v4M8 12h8"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Regular seats --}}
                        @foreach($seatMap['grid'] as $row => $cols)
                            <div class="grid grid-cols-5 gap-2 mb-2">
                                @for($col = 0; $col <= 4; $col++)
                                    @if($col === $seatMap['layout']['aisle_col'])
                                        <div class="flex items-center justify-center">
                                            <span class="text-[9px] text-slate-300 font-bold">{{ $row }}</span>
                                        </div>
                                    @elseif(isset($cols[$col]))
                                        @php
                                            $seat = $cols[$col];
                                            $passenger = $occupiedSeats->get($seat->id);
                                            $passengerData = $passenger ? [
                                                'id'           => $passenger->id,
                                                'nama'         => $passenger->nama_penumpang,
                                                'no_hp'        => $passenger->no_hp,
                                                'alamat_naik'  => $passenger->alamat_naik,
                                                'alamat_turun' => $passenger->alamat_turun,
                                                'catatan'      => $passenger->catatan,
                                                'diinput_oleh' => $passenger->inputBy?->name,
                                            ] : null;
                                        @endphp
                                        <button type="button"
                                                @click="openSeat({{ json_encode(['id' => $seat->id, 'nomor' => $seat->nomor_kursi]) }}, {{ json_encode($passengerData) }})"
                                                class="w-full aspect-square rounded-2xl text-xs font-bold transition-all active:scale-90 focus:outline-none shadow-sm
                                                    {{ $passenger
                                                        ? 'bg-emerald-500 text-white shadow-emerald-200'
                                                        : 'bg-slate-50 border border-slate-200 text-slate-500 hover:border-indigo-300 hover:bg-indigo-50' }}">
                                            {{ $seat->nomor_kursi }}
                                        </button>
                                    @else
                                        <div></div>
                                    @endif
                                @endfor
                            </div>
                        @endforeach

                        {{-- Sleeper --}}
                        @if($seatMap['layout']['sleeper_section'] && $seatMap['sleeperSeats']->count())
                            <div class="mt-4 pt-4 border-t-2 border-dashed border-amber-200">
                                <p class="text-[10px] font-bold text-amber-500 text-center mb-2 tracking-widest">✦ SLEEPER</p>
                                <div class="grid grid-cols-5 gap-2">
                                    @foreach($seatMap['sleeperSeats'] as $seat)
                                        @php
                                            $passenger = $occupiedSeats->get($seat->id);
                                            $passengerData = $passenger ? [
                                                'id'           => $passenger->id,
                                                'nama'         => $passenger->nama_penumpang,
                                                'no_hp'        => $passenger->no_hp,
                                                'alamat_naik'  => $passenger->alamat_naik,
                                                'alamat_turun' => $passenger->alamat_turun,
                                                'catatan'      => $passenger->catatan,
                                                'diinput_oleh' => $passenger->inputBy?->name,
                                            ] : null;
                                        @endphp
                                        <button type="button"
                                                @click="openSeat({{ json_encode(['id' => $seat->id, 'nomor' => $seat->nomor_kursi]) }}, {{ json_encode($passengerData) }})"
                                                style="grid-column: {{ $seat->posisi_col < 2 ? $seat->posisi_col + 1 : $seat->posisi_col }}"
                                                class="aspect-square rounded-2xl text-xs font-bold transition-all active:scale-90 shadow-sm
                                                    {{ $passenger
                                                        ? 'bg-emerald-500 text-white'
                                                        : 'bg-amber-50 border border-amber-300 text-amber-600 hover:bg-amber-100' }}">
                                            {{ $seat->nomor_kursi }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Bottom Sheet: Passenger --}}
        <div x-show="dialogOpen" x-cloak class="fixed inset-0 z-50 flex items-end justify-center" style="display:none">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeDialog()"></div>
            <div class="relative bg-white rounded-t-3xl w-full max-w-md shadow-2xl"
                 @click.stop
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0">

                <div class="flex justify-center pt-3 pb-1">
                    <div class="w-10 h-1 bg-slate-200 rounded-full"></div>
                </div>

                <div class="px-5 pb-2">
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-xs text-slate-400 font-medium">Kursi</p>
                            <h3 class="text-lg font-bold text-slate-900" x-text="currentSeat?.nomor"></h3>
                        </div>
                        <button @click="closeDialog()"
                                class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 active:bg-slate-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-5 pb-8">
                    {{-- View mode --}}
                    <div x-show="currentPassenger && !editMode">
                        <div class="bg-slate-50 rounded-2xl p-4 space-y-3 mb-4">
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">Nama</span>
                                <span class="text-sm font-semibold text-slate-900" x-text="currentPassenger?.nama"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">No. HP</span>
                                <span class="text-sm text-slate-700" x-text="currentPassenger?.no_hp || '—'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">Naik dari</span>
                                <span class="text-sm text-slate-700" x-text="currentPassenger?.alamat_naik || '—'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">Turun di</span>
                                <span class="text-sm text-slate-700" x-text="currentPassenger?.alamat_turun || '—'"></span>
                            </div>
                            <div x-show="currentPassenger?.catatan">
                                <span class="text-xs text-slate-400">Catatan</span>
                                <p class="text-sm text-slate-700 mt-0.5" x-text="currentPassenger?.catatan"></p>
                            </div>
                            {{-- Didaftarkan oleh --}}
                            <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
                                <span class="text-xs text-slate-400">Didaftarkan oleh</span>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-5 h-5 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] font-bold text-indigo-600"
                                         x-text="(currentPassenger?.diinput_oleh || '?').charAt(0).toUpperCase()"></div>
                                    <span class="text-sm font-semibold text-indigo-700" x-text="currentPassenger?.diinput_oleh || '—'"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button @click="editMode = true"
                                    class="flex-1 h-11 bg-indigo-600 text-white text-sm font-bold rounded-2xl active:scale-[.98] transition-transform">
                                Edit
                            </button>
                            <form :action="deleteUrl()" method="POST" @submit.prevent="submitDelete($el)" class="flex-1">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-full h-11 bg-red-50 text-red-600 text-sm font-bold rounded-2xl active:scale-[.98] transition-transform border border-red-200">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Form mode --}}
                    <form x-show="!currentPassenger || editMode" :action="formUrl()" method="POST" @submit="closeDialog()" class="space-y-3">
                        @csrf
                        <template x-if="currentPassenger">
                            <input type="hidden" name="_method" value="PATCH">
                        </template>
                        <input type="hidden" name="seat_id" :value="currentSeat?.id">

                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama Penumpang *</label>
                            <input type="text" name="nama_penumpang" x-model="form.nama" required placeholder="Nama lengkap"
                                   class="mt-1 w-full h-11 px-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">No. HP</label>
                            <input type="text" name="no_hp" x-model="form.no_hp" placeholder="08xx..."
                                   class="mt-1 w-full h-11 px-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Naik dari</label>
                                <input type="text" name="alamat_naik" x-model="form.alamat_naik" placeholder="Terminal..."
                                       class="mt-1 w-full h-11 px-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Turun di</label>
                                <input type="text" name="alamat_turun" x-model="form.alamat_turun" placeholder="Terminal..."
                                       class="mt-1 w-full h-11 px-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Catatan</label>
                            <textarea name="catatan" x-model="form.catatan" rows="2" placeholder="Opsional..."
                                      class="mt-1 w-full px-4 py-3 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition resize-none"></textarea>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button type="submit"
                                    class="flex-1 h-11 bg-indigo-600 text-white text-sm font-bold rounded-2xl active:scale-[.98] transition-transform">
                                Simpan
                            </button>
                            <button type="button" @click="closeDialog()"
                                    class="flex-1 h-11 bg-slate-100 text-slate-600 text-sm font-bold rounded-2xl active:scale-[.98] transition-transform">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($trip)
    <script>
    function seatApp() {
        return {
            dialogOpen: false, editMode: false,
            currentSeat: null, currentPassenger: null,
            form: { nama: '', no_hp: '', alamat_naik: '', alamat_turun: '', catatan: '' },
            tripId: {{ $trip->id }},
            openSeat(seat, passenger) {
                this.currentSeat = seat; this.currentPassenger = passenger; this.editMode = false;
                this.form = passenger
                    ? { nama: passenger.nama, no_hp: passenger.no_hp||'', alamat_naik: passenger.alamat_naik||'', alamat_turun: passenger.alamat_turun||'', catatan: passenger.catatan||'' }
                    : { nama: '', no_hp: '', alamat_naik: '', alamat_turun: '', catatan: '' };
                this.dialogOpen = true;
            },
            closeDialog() { this.dialogOpen = false; },
            formUrl() { return this.currentPassenger ? `/trips/${this.tripId}/passengers/${this.currentPassenger.id}` : `/trips/${this.tripId}/passengers`; },
            deleteUrl() { return `/trips/${this.tripId}/passengers/${this.currentPassenger?.id}`; },
            submitDelete(form) { if (confirm('Hapus penumpang ini?')) form.submit(); }
        }
    }
    </script>
    @else
    <script>function seatApp() { return { dialogOpen: false, openSeat() {}, closeDialog() {} } }</script>
    @endif
</x-app-layout>
