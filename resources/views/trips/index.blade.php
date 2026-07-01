<x-app-layout>
    <x-slot name="title">Denah Kursi</x-slot>

    <div class="space-y-5" x-data="seatApp()">

        {{-- Trip selector --}}
        <x-card class="p-4">
            <form method="POST" action="{{ route('trips.store') }}" class="flex flex-wrap gap-3 items-end">
                @csrf
                <x-select label="Jadwal" name="schedule_id" class="w-full sm:w-48">
                    <option value="">Pilih jadwal...</option>
                    @foreach($schedules as $s)
                        <option value="{{ $s->id }}" {{ $trip && $trip->schedule_id == $s->id ? 'selected' : '' }}>
                            {{ $s->route->name }} — {{ $s->label }}
                        </option>
                    @endforeach
                </x-select>

                <x-input label="Tanggal" type="date" name="tanggal"
                         value="{{ $trip ? $trip->tanggal_berangkat->format('Y-m-d') : today()->format('Y-m-d') }}"
                         class="w-full sm:w-40" />

                <x-select label="Armada" name="bus_id" class="w-full sm:w-44">
                    <option value="">Pilih armada...</option>
                    @foreach($buses as $b)
                        <option value="{{ $b->id }}" {{ $trip && $trip->bus_id == $b->id ? 'selected' : '' }}>
                            {{ $b->nomor_lambung }} ({{ $b->busType->name }})
                        </option>
                    @endforeach
                </x-select>

                <x-button type="submit">Tampilkan</x-button>

                @if($trip)
                    <a href="{{ route('manifest.show', $trip) }}">
                        <x-button variant="outline">Manifest</x-button>
                    </a>
                    @role('admin|pengurus')
                    <form action="{{ route('trips.status', $trip) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $trip->status === 'dibuka' ? 'ditutup' : 'dibuka' }}">
                        <x-button type="submit" variant="{{ $trip->status === 'dibuka' ? 'destructive' : 'success' }}"
                                  onclick="return confirm('{{ $trip->status === 'dibuka' ? 'Tutup trip ini?' : 'Buka kembali trip ini?' }}')">
                            {{ $trip->status === 'dibuka' ? 'Tutup Trip' : 'Buka Kembali' }}
                        </x-button>
                    </form>
                    @endrole
                @endif
            </form>
        </x-card>

        @if($trip && $seatMap)
            {{-- Stats bar --}}
            @php
                $capacity = $trip->bus->busType->total_seat;
                $filled = $occupiedSeats->count();
                $empty = $capacity - $filled;
            @endphp
            <div class="flex items-center gap-3 flex-wrap">
                <x-card class="px-4 py-3 flex items-center gap-3">
                    <div class="w-3 h-3 rounded-sm bg-emerald-500"></div>
                    <span class="text-sm text-slate-600">Terisi: <strong class="text-slate-900">{{ $filled }}</strong></span>
                </x-card>
                <x-card class="px-4 py-3 flex items-center gap-3">
                    <div class="w-3 h-3 rounded-sm bg-white border border-slate-300"></div>
                    <span class="text-sm text-slate-600">Kosong: <strong class="text-slate-900">{{ $empty }}</strong></span>
                </x-card>
                <x-card class="px-4 py-3">
                    <span class="text-sm text-slate-600">Kapasitas: <strong class="text-slate-900">{{ $capacity }}</strong></span>
                </x-card>
                <x-badge variant="{{ $trip->status === 'dibuka' ? 'success' : 'danger' }}" class="text-sm px-3 py-1.5">
                    {{ ucfirst($trip->status) }}
                </x-badge>
            </div>

            {{-- Seat Map --}}
            <x-card class="p-5">
                <div class="flex justify-center">
                    <div class="w-full max-w-sm">
                        {{-- Bus front indicator --}}
                        <div class="flex justify-center mb-4">
                            <div class="bg-slate-100 border border-slate-200 rounded-lg px-6 py-2 text-xs font-medium text-slate-500 tracking-wide">
                                ▲ DEPAN BUS
                            </div>
                        </div>

                        {{-- Driver row --}}
                        <div class="grid grid-cols-5 gap-1.5 mb-3">
                            <div class="col-span-2"></div>
                            <div></div>{{-- aisle --}}
                            <div class="col-span-2 flex justify-end">
                                <div class="w-10 h-10 rounded-md bg-slate-200 border border-slate-300 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                        <path stroke-linecap="round" stroke-width="2" d="M12 8v4M8 12h8"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Regular seats grid --}}
                        @foreach($seatMap['grid'] as $row => $cols)
                            <div class="grid grid-cols-5 gap-1.5 mb-1.5">
                                @for($col = 0; $col <= 4; $col++)
                                    @if($col === $seatMap['layout']['aisle_col'])
                                        <div class="flex items-center justify-center">
                                            <span class="text-[10px] text-slate-300">{{ $row }}</span>
                                        </div>
                                    @elseif(isset($cols[$col]))
                                        @php $seat = $cols[$col]; $passenger = $occupiedSeats->get($seat->id); @endphp
                                        <button
                                            type="button"
                                            @click="openSeat({{ json_encode(['id' => $seat->id, 'nomor' => $seat->nomor_kursi, 'row' => $row, 'col' => $col]) }}, {{ $passenger ? json_encode(['id' => $passenger->id, 'nama' => $passenger->nama_penumpang, 'no_hp' => $passenger->no_hp, 'alamat_naik' => $passenger->alamat_naik, 'alamat_turun' => $passenger->alamat_turun, 'catatan' => $passenger->catatan]) : 'null' }})"
                                            class="w-full aspect-square rounded-md border text-xs font-semibold transition-all focus:outline-none focus:ring-2 focus:ring-offset-1
                                                   {{ $passenger
                                                       ? 'bg-emerald-500 border-emerald-600 text-white hover:bg-emerald-600 focus:ring-emerald-500'
                                                       : 'bg-white border-slate-300 text-slate-600 hover:border-slate-400 hover:bg-slate-50 focus:ring-slate-400' }}">
                                            {{ $seat->nomor_kursi }}
                                        </button>
                                    @else
                                        <div></div>
                                    @endif
                                @endfor
                            </div>
                        @endforeach

                        {{-- Sleeper section --}}
                        @if($seatMap['layout']['sleeper_section'] && $seatMap['sleeperSeats']->count())
                            <div class="mt-4 pt-4 border-t border-dashed border-slate-200">
                                <p class="text-xs text-slate-400 text-center mb-2 font-medium">SLEEPER</p>
                                <div class="grid grid-cols-5 gap-1.5">
                                    @foreach($seatMap['sleeperSeats'] as $seat)
                                        @php $passenger = $occupiedSeats->get($seat->id); @endphp
                                        <button
                                            type="button"
                                            @click="openSeat({{ json_encode(['id' => $seat->id, 'nomor' => $seat->nomor_kursi, 'row' => $seat->posisi_row, 'col' => $seat->posisi_col]) }}, {{ $passenger ? json_encode(['id' => $passenger->id, 'nama' => $passenger->nama_penumpang, 'no_hp' => $passenger->no_hp, 'alamat_naik' => $passenger->alamat_naik, 'alamat_turun' => $passenger->alamat_turun, 'catatan' => $passenger->catatan]) : 'null' }})"
                                            style="grid-column: {{ $seat->posisi_col < 2 ? $seat->posisi_col + 1 : $seat->posisi_col }}"
                                            class="aspect-square rounded-md border text-xs font-semibold transition-all
                                                   {{ $passenger
                                                       ? 'bg-emerald-500 border-emerald-600 text-white hover:bg-emerald-600'
                                                       : 'bg-amber-50 border-amber-300 text-amber-700 hover:bg-amber-100' }}">
                                            {{ $seat->nomor_kursi }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>
        @endif

        {{-- Dialog: Add/Edit Passenger --}}
        <div x-show="dialogOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div class="absolute inset-0 bg-black/40" @click="closeDialog()"></div>
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md border border-slate-200"
                 @click.stop
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-900" x-text="currentPassenger ? 'Detail Kursi ' + currentSeat?.nomor : 'Isi Kursi ' + currentSeat?.nomor"></h3>
                    <button @click="closeDialog()" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-5 py-4">
                    {{-- View mode --}}
                    <div x-show="currentPassenger && !editMode">
                        <dl class="space-y-2 text-sm">
                            <div class="flex gap-2"><dt class="w-28 text-slate-500 shrink-0">Nama</dt><dd class="font-medium" x-text="currentPassenger?.nama"></dd></div>
                            <div class="flex gap-2"><dt class="w-28 text-slate-500 shrink-0">No. HP</dt><dd x-text="currentPassenger?.no_hp || '—'"></dd></div>
                            <div class="flex gap-2"><dt class="w-28 text-slate-500 shrink-0">Naik dari</dt><dd x-text="currentPassenger?.alamat_naik || '—'"></dd></div>
                            <div class="flex gap-2"><dt class="w-28 text-slate-500 shrink-0">Turun di</dt><dd x-text="currentPassenger?.alamat_turun || '—'"></dd></div>
                            <div class="flex gap-2"><dt class="w-28 text-slate-500 shrink-0">Catatan</dt><dd x-text="currentPassenger?.catatan || '—'"></dd></div>
                        </dl>
                        <div class="flex gap-2 mt-4">
                            <x-button variant="outline" size="sm" @click="editMode = true">Edit</x-button>
                            <form :action="deleteUrl()" method="POST" @submit.prevent="submitDelete($el)">
                                @csrf @method('DELETE')
                                <x-button variant="destructive" size="sm" type="submit">Hapus</x-button>
                            </form>
                        </div>
                    </div>

                    {{-- Form mode (add or edit) --}}
                    <form x-show="!currentPassenger || editMode" :action="formUrl()" method="POST" @submit="closeDialog()">
                        @csrf
                        <template x-if="currentPassenger">
                            <input type="hidden" name="_method" value="PATCH">
                        </template>
                        <input type="hidden" name="seat_id" :value="currentSeat?.id">

                        <div class="space-y-3">
                            <x-input label="Nama Penumpang *" name="nama_penumpang" x-model="form.nama" placeholder="Nama lengkap" required />
                            <x-input label="No. HP" name="no_hp" x-model="form.no_hp" placeholder="08xx..." />
                            <x-input label="Naik dari" name="alamat_naik" x-model="form.alamat_naik" placeholder="Terminal / alamat" />
                            <x-input label="Turun di" name="alamat_turun" x-model="form.alamat_turun" placeholder="Terminal / alamat" />
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700">Catatan</label>
                                <textarea name="catatan" x-model="form.catatan" rows="2"
                                          class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 resize-none"></textarea>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <x-button type="submit" variant="success">Simpan</x-button>
                            <x-button type="button" variant="ghost" @click="closeDialog()">Batal</x-button>
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
            dialogOpen: false,
            editMode: false,
            currentSeat: null,
            currentPassenger: null,
            form: { nama: '', no_hp: '', alamat_naik: '', alamat_turun: '', catatan: '' },
            tripId: {{ $trip->id }},

            openSeat(seat, passenger) {
                this.currentSeat = seat;
                this.currentPassenger = passenger;
                this.editMode = false;
                if (passenger) {
                    this.form = { nama: passenger.nama, no_hp: passenger.no_hp || '', alamat_naik: passenger.alamat_naik || '', alamat_turun: passenger.alamat_turun || '', catatan: passenger.catatan || '' };
                } else {
                    this.form = { nama: '', no_hp: '', alamat_naik: '', alamat_turun: '', catatan: '' };
                }
                this.dialogOpen = true;
            },

            closeDialog() {
                this.dialogOpen = false;
            },

            formUrl() {
                if (this.currentPassenger) {
                    return `/trips/${this.tripId}/passengers/${this.currentPassenger.id}`;
                }
                return `/trips/${this.tripId}/passengers`;
            },

            deleteUrl() {
                return `/trips/${this.tripId}/passengers/${this.currentPassenger?.id}`;
            },

            submitDelete(form) {
                if (confirm('Hapus penumpang ini?')) {
                    form.submit();
                }
            }
        }
    }
    </script>
    @else
    <script>function seatApp() { return { dialogOpen: false, openSeat() {}, closeDialog() {} } }</script>
    @endif
</x-app-layout>
