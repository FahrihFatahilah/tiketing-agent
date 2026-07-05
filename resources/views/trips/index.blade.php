<x-app-layout>
    <x-slot name="title">Jadwal & Trip</x-slot>

    <div x-data="seatApp()">

        {{-- Trip per hari --}}
        @if($tripsByDate->isNotEmpty())
        <div class="space-y-4 mb-6">
            @foreach($tripsByDate as $tanggal => $dayTrips)
                @php $date = \Carbon\Carbon::parse($tanggal); @endphp
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold text-zinc-900">
                            {{ $date->isToday() ? 'Hari Ini' : ($date->isYesterday() ? 'Kemarin' : $date->translatedFormat('d M Y')) }}
                        </span>
                        <span class="text-[10px] text-zinc-400">{{ $date->translatedFormat('l') }}</span>
                        @if($date->isToday())
                            <span class="badge badge-success text-[10px]">Today</span>
                        @endif
                    </div>
                    <div class="space-y-2">
                        @foreach($dayTrips as $t)
                            @php
                                $cap    = $t->bus->busType->total_seat;
                                $filled = $t->passengers->count();
                                $pct    = $cap > 0 ? round($filled / $cap * 100) : 0;
                            @endphp
                            <a href="{{ route('trips.seatmap', $t) }}"
                               class="card block p-3 hover:bg-zinc-50 active:bg-zinc-100 transition-colors">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <p class="text-[11px] text-zinc-400">{{ $t->schedule->route->name }}</p>
                                        <p class="text-sm font-semibold text-zinc-900">{{ $t->schedule->label }}</p>
                                        <p class="text-[11px] text-zinc-400 mt-0.5">{{ $t->bus->nomor_lambung }} · {{ $t->bus->busType->name }}</p>
                                    </div>
                                    <span class="badge {{ $t->status === 'dibuka' ? 'badge-success' : 'badge-destructive' }}">
                                        {{ $t->status }}
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-[11px] text-zinc-500">
                                        <span>{{ $filled }} / {{ $cap }} kursi terisi</span>
                                        <span class="font-medium text-zinc-700">{{ $pct }}%</span>
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-zinc-100">
                                        <div class="h-full rounded-full bg-zinc-900 transition-all" style="width:{{ $pct }}%"></div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <div class="separator mb-4"></div>

        {{-- Selector --}}
        <p class="text-xs font-semibold uppercase tracking-widest text-zinc-400 mb-3">Buka / Buat Trip</p>
        <div class="card p-4 mb-4" x-data="tripForm()">
            <form method="POST" action="{{ route('trips.store') }}" class="space-y-3">
                @csrf
                <div class="space-y-1.5">
                    <label class="label">Tanggal</label>
                    <input type="date" name="tanggal" x-model="tanggal" class="input"
                           value="{{ $trip ? $trip->tanggal_berangkat->format('Y-m-d') : today()->format('Y-m-d') }}">
                </div>
                <div class="space-y-1.5">
                    <label class="label">Jadwal</label>
                    <select name="schedule_id" x-model="scheduleId" required class="input">
                        <option value="">Pilih jadwal...</option>
                        @foreach($schedules as $s)
                            <option value="{{ $s->id }}"
                                    {{ $trip && $trip->schedule_id == $s->id ? 'selected' : '' }}>
                                {{ $s->route->name }} — {{ $s->label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="label">Armada</label>
                    <select name="bus_id" x-model="busId" required class="input">
                        <option value="">Pilih...</option>
                        @foreach($buses as $b)
                            <option value="{{ $b->id }}"
                                    :disabled="isBusTaken(scheduleId, {{ $b->id }})"
                                    :class="isBusTaken(scheduleId, {{ $b->id }}) ? 'text-zinc-300' : ''"
                                    {{ $trip && $trip->bus_id == $b->id ? 'selected' : '' }}>
                                {{ $b->nomor_lambung }} ({{ $b->busType->name }})
                            </option>
                        @endforeach
                    </select>
                    <p x-show="busId && scheduleId && isBusTaken(scheduleId, busId)" class="text-[11px] text-red-500">
                        Armada ini sudah digunakan di jadwal dan tanggal ini.
                    </p>
                </div>
                <button type="submit" class="btn-default w-full">Tampilkan</button>
            </form>
        </div>

        @if($trip && $seatMap)
            @php
                $capacity = $trip->bus->busType->total_seat;
                $filled   = $occupiedSeats->count();
                $pct      = $capacity > 0 ? round($filled / $capacity * 100) : 0;
            @endphp

            {{-- Trip info --}}
            <div class="card p-4 mb-4">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-[11px] text-zinc-400">{{ $trip->schedule->route->name }}</p>
                        <p class="text-sm font-semibold text-zinc-900">{{ $trip->schedule->label }}</p>
                        <p class="text-[11px] text-zinc-400 mt-0.5">{{ $trip->bus->nomor_lambung }} · {{ $trip->tanggal_berangkat->format('d M Y') }}</p>
                    </div>
                    <span class="{{ $trip->status === 'dibuka' ? 'badge-success' : 'badge-destructive' }} badge">
                        {{ $trip->status }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2 mb-3 text-center">
                    <div class="rounded-md bg-zinc-50 border border-zinc-100 py-2">
                        <p class="text-base font-semibold text-zinc-900">{{ $filled }}</p>
                        <p class="text-[10px] text-zinc-400">Terisi</p>
                    </div>
                    <div class="rounded-md bg-zinc-50 border border-zinc-100 py-2">
                        <p class="text-base font-semibold text-zinc-900">{{ $capacity - $filled }}</p>
                        <p class="text-[10px] text-zinc-400">Kosong</p>
                    </div>
                    <div class="rounded-md bg-zinc-50 border border-zinc-100 py-2">
                        <p class="text-base font-semibold text-zinc-900">{{ $pct }}%</p>
                        <p class="text-[10px] text-zinc-400">Penuh</p>
                    </div>
                </div>

                <div class="h-1.5 w-full overflow-hidden rounded-full bg-zinc-100 mb-3">
                    <div class="h-full rounded-full bg-zinc-900 transition-all" style="width:{{ $pct }}%"></div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('manifest.show', $trip) }}" class="btn-outline btn-sm flex-1 justify-center">Manifest</a>
                    @role('admin|pengurus')
                    <form action="{{ route('trips.status', $trip) }}" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $trip->status === 'dibuka' ? 'ditutup' : 'dibuka' }}">
                        <button type="submit"
                                onclick="return confirm('{{ $trip->status === 'dibuka' ? 'Tutup trip ini?' : 'Buka kembali trip ini?' }}')"
                                class="w-full {{ $trip->status === 'dibuka' ? 'btn-destructive' : 'btn-default' }} btn-sm justify-center">
                            {{ $trip->status === 'dibuka' ? 'Tutup Trip' : 'Buka Kembali' }}
                        </button>
                    </form>
                    @endrole
                </div>
            </div>

            {{-- Seat map --}}
            <div class="card p-4 mb-4">
                {{-- Legend --}}
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="flex items-center gap-1.5">
                        <div class="h-3 w-3 rounded-sm bg-zinc-900"></div>
                        <span class="text-[11px] text-zinc-500">Terisi</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="h-3 w-3 rounded-sm border border-zinc-300 bg-white"></div>
                        <span class="text-[11px] text-zinc-500">Kosong</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="h-3 w-3 rounded-sm border border-zinc-300 bg-zinc-100"></div>
                        <span class="text-[11px] text-zinc-500">Sleeper</span>
                    </div>
                </div>

                <div class="flex justify-center">
                    <div class="w-full max-w-[240px]">
                        <div class="mb-4 flex justify-center">
                            <span class="rounded-md border border-zinc-200 bg-zinc-50 px-4 py-1 text-[10px] font-semibold uppercase tracking-widest text-zinc-400">
                                ▲ Depan
                            </span>
                        </div>

                        {{-- Driver --}}
                        <div class="mb-3 grid grid-cols-5 gap-1.5">
                            <div class="col-span-2"></div><div></div>
                            <div class="col-span-2 flex justify-end">
                                <div class="flex h-10 w-10 items-center justify-center rounded-md border border-zinc-200 bg-zinc-50">
                                    <svg class="h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke-width="1.5"/>
                                        <path stroke-linecap="round" stroke-width="1.5" d="M12 8v4M8 12h8"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Regular seats --}}
                        @foreach($seatMap['grid'] as $row => $cols)
                            <div class="mb-1.5 grid grid-cols-5 gap-1.5">
                                @for($col = 0; $col <= 4; $col++)
                                    @if($col === $seatMap['layout']['aisle_col'])
                                        <div class="flex items-center justify-center">
                                            <span class="text-[9px] font-medium text-zinc-300">{{ $row }}</span>
                                        </div>
                                    @elseif(isset($cols[$col]))
                                        @php
                                            $seat = $cols[$col];
                                            $passenger = $occupiedSeats->get($seat->id);
                                            $pd = $passenger ? [
                                                'id'           => $passenger->id,
                                                'nama'         => $passenger->nama_penumpang,
                                                'no_hp'        => $passenger->no_hp,
                                                'alamat_naik'  => $passenger->alamat_naik,
                                                'alamat_turun' => $passenger->alamat_turun,
                                                'catatan'      => $passenger->catatan,
                                                'diinput_oleh' => $passenger->inputBy?->name,
                                                'is_owner'     => $passenger->diinput_oleh === auth()->id() || auth()->user()->hasRole('admin'),
                                            ] : null;
                                        @endphp
                                        <button type="button"
                                                @click="openSeat({{ json_encode(['id'=>$seat->id,'nomor'=>$seat->nomor_kursi]) }}, {{ json_encode($pd) }})"
                                                class="aspect-square w-full rounded-md border text-[11px] font-semibold transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950
                                                    {{ $passenger
                                                        ? 'border-zinc-900 bg-zinc-900 text-white'
                                                        : 'border-zinc-200 bg-white text-zinc-600 hover:border-zinc-400 hover:bg-zinc-50' }}">
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
                            <div class="mt-3 border-t border-dashed border-zinc-200 pt-3">
                                <p class="mb-2 text-center text-[10px] font-semibold uppercase tracking-widest text-zinc-400">Sleeper</p>
                                <div class="grid grid-cols-5 gap-1.5">
                                    @foreach($seatMap['sleeperSeats'] as $seat)
                                        @php
                                            $passenger = $occupiedSeats->get($seat->id);
                                            $pd = $passenger ? [
                                                'id'           => $passenger->id,
                                                'nama'         => $passenger->nama_penumpang,
                                                'no_hp'        => $passenger->no_hp,
                                                'alamat_naik'  => $passenger->alamat_naik,
                                                'alamat_turun' => $passenger->alamat_turun,
                                                'catatan'      => $passenger->catatan,
                                                'diinput_oleh' => $passenger->inputBy?->name,
                                                'is_owner'     => $passenger->diinput_oleh === auth()->id() || auth()->user()->hasRole('admin'),
                                            ] : null;
                                        @endphp
                                        <button type="button"
                                                @click="openSeat({{ json_encode(['id'=>$seat->id,'nomor'=>$seat->nomor_kursi]) }}, {{ json_encode($pd) }})"
                                                style="grid-column:{{ $seat->posisi_col < 2 ? $seat->posisi_col + 1 : $seat->posisi_col }}"
                                                class="aspect-square rounded-md border text-[11px] font-semibold transition-colors
                                                    {{ $passenger
                                                        ? 'border-zinc-900 bg-zinc-900 text-white'
                                                        : 'border-zinc-200 bg-zinc-50 text-zinc-500 hover:border-zinc-400' }}">
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

        {{-- Sheet --}}
        <div x-show="dialogOpen" x-cloak class="fixed inset-0 z-50 flex items-end justify-center" style="display:none">
            <div class="absolute inset-0 bg-black/40" @click="closeDialog()"></div>
            <div class="relative w-full max-w-md rounded-t-xl border-t border-zinc-200 bg-white shadow-xl"
                 @click.stop
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0">

                <div class="flex justify-center pt-3">
                    <div class="h-1 w-8 rounded-full bg-zinc-200"></div>
                </div>

                <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-100">
                    <div>
                        <p class="text-[11px] text-zinc-400">Kursi</p>
                        <p class="text-base font-semibold text-zinc-900" x-text="currentSeat?.nomor"></p>
                    </div>
                    <button @click="closeDialog()" class="btn-ghost btn-icon">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-4 pb-8 pt-3">
                    {{-- View --}}
                    <div x-show="currentPassenger && !editMode" class="space-y-3">
                        <div class="rounded-lg border border-zinc-100 bg-zinc-50 divide-y divide-zinc-100">
                            <div class="flex items-center justify-between px-3 py-2.5">
                                <span class="text-xs text-zinc-400">Nama</span>
                                <span class="text-sm font-medium text-zinc-900" x-text="currentPassenger?.nama"></span>
                            </div>
                            <div class="flex items-center justify-between px-3 py-2.5">
                                <span class="text-xs text-zinc-400">No. HP</span>
                                <span class="text-sm text-zinc-700" x-text="currentPassenger?.no_hp || '—'"></span>
                            </div>
                            <div class="flex items-center justify-between px-3 py-2.5">
                                <span class="text-xs text-zinc-400">Naik dari</span>
                                <span class="text-sm text-zinc-700" x-text="currentPassenger?.alamat_naik || '—'"></span>
                            </div>
                            <div class="flex items-center justify-between px-3 py-2.5">
                                <span class="text-xs text-zinc-400">Turun di</span>
                                <span class="text-sm text-zinc-700" x-text="currentPassenger?.alamat_turun || '—'"></span>
                            </div>
                            <div class="flex items-center justify-between px-3 py-2.5" x-show="currentPassenger?.catatan">
                                <span class="text-xs text-zinc-400">Catatan</span>
                                <span class="text-sm text-zinc-700" x-text="currentPassenger?.catatan"></span>
                            </div>
                            <div class="flex items-center justify-between px-3 py-2.5">
                                <span class="text-xs text-zinc-400">Didaftarkan oleh</span>
                                <span class="text-sm font-medium text-zinc-900" x-text="currentPassenger?.diinput_oleh || '—'"></span>
                            </div>
                        </div>
                        <div class="flex gap-2" x-show="currentPassenger?.is_owner">
                            <button @click="editMode = true" class="btn-outline flex-1">Edit</button>
                            <form :action="deleteUrl()" method="POST" @submit.prevent="submitDelete($el)" class="flex-1">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-destructive w-full">Hapus</button>
                            </form>
                        </div>
                    </div>

                    {{-- Form --}}
                    <form x-show="!currentPassenger || (editMode && currentPassenger?.is_owner)" :action="formUrl()" method="POST" @submit="closeDialog()" class="space-y-3">
                        @csrf
                        <template x-if="currentPassenger"><input type="hidden" name="_method" value="PATCH"></template>
                        <input type="hidden" name="seat_id" :value="currentSeat?.id">

                        <div class="space-y-1.5">
                            <label class="label">Nama Penumpang <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_penumpang" x-model="form.nama" required placeholder="Nama lengkap" class="input">
                        </div>
                        <div class="space-y-1.5">
                            <label class="label">No. HP</label>
                            <input type="text" name="no_hp" x-model="form.no_hp" placeholder="08xx..." class="input">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="label">Naik dari</label>
                                <input type="text" name="alamat_naik" x-model="form.alamat_naik" placeholder="Terminal..." class="input">
                            </div>
                            <div class="space-y-1.5">
                                <label class="label">Turun di</label>
                                <input type="text" name="alamat_turun" x-model="form.alamat_turun" placeholder="Terminal..." class="input">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="label">Catatan</label>
                            <textarea name="catatan" x-model="form.catatan" rows="2" placeholder="Opsional..."
                                      class="input h-auto py-2 resize-none"></textarea>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button type="submit" class="btn-default flex-1">Simpan</button>
                            <button type="button" @click="closeDialog()" class="btn-outline flex-1">Batal</button>
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
            form: { nama:'', no_hp:'', alamat_naik:'', alamat_turun:'', catatan:'' },
            tripId: {{ $trip->id }},
            openSeat(seat, passenger) {
                this.currentSeat = seat; this.currentPassenger = passenger; this.editMode = false;
                this.form = passenger
                    ? { nama: passenger.nama, no_hp: passenger.no_hp||'', alamat_naik: passenger.alamat_naik||'', alamat_turun: passenger.alamat_turun||'', catatan: passenger.catatan||'' }
                    : { nama:'', no_hp:'', alamat_naik:'', alamat_turun:'', catatan:'' };
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
    <script>function seatApp() { return { dialogOpen:false, openSeat(){}, closeDialog(){} } }</script>
    @endif

    <script>
    const takenBuses = @json($takenBuses);
    function tripForm() {
        return {
            tanggal: '{{ $trip ? $trip->tanggal_berangkat->format('Y-m-d') : today()->format('Y-m-d') }}',
            scheduleId: '{{ $trip ? $trip->schedule_id : '' }}',
            busId: '{{ $trip ? $trip->bus_id : '' }}',
            get takenList() { return takenBuses[this.tanggal] || []; },
            isBusTaken(scheduleId, busId) {
                return this.takenList.some(t => t.schedule_id == scheduleId && t.bus_id == busId);
            },
        }
    }
    </script>
</x-app-layout>
