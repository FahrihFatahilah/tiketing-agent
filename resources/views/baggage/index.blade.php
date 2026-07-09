<x-app-layout>
    <x-slot name="title">Bagasi — {{ $trip->schedule->label }}</x-slot>

    <div class="space-y-4" x-data="{ showForm: false, editItem: null }">
        {{-- Trip info --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-slate-900">{{ $trip->schedule->route->name }} — {{ $trip->schedule->label }}</p>
                <p class="text-xs text-slate-500">{{ $trip->tanggal_berangkat->format('d F Y') }} · {{ $trip->bus->nomor_lambung }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('trips.seatmap', $trip) }}">
                    <x-button variant="outline" size="sm">← Denah Kursi</x-button>
                </a>
                <x-button size="sm" @click="showForm = true; editItem = null">+ Tambah Bagasi</x-button>
            </div>
        </div>

        {{-- List --}}
        @if($trip->baggages->isEmpty())
            <x-card class="p-8 text-center">
                <p class="text-slate-400 text-sm">Belum ada data bagasi untuk trip ini.</p>
            </x-card>
        @else
            <x-card class="overflow-x-auto">
                <table class="w-full text-sm min-w-[600px]">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">No</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Jenis</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Pengirim</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Penerima</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Jml</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Ket</th>
                            <th class="px-4 py-3 w-24"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($trip->baggages as $i => $bag)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ $bag->jenis_barang }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-slate-900">{{ $bag->nama_pengirim }}</p>
                                    <p class="text-xs text-slate-400">{{ $bag->no_hp_pengirim ?: '—' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-slate-900">{{ $bag->nama_penerima }}</p>
                                    <p class="text-xs text-slate-400">{{ $bag->no_hp_penerima ?: '—' }}</p>
                                </td>
                                <td class="px-4 py-3">{{ $bag->jumlah }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $bag->keterangan ?: '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex gap-1 justify-end">
                                        <x-button variant="ghost" size="sm"
                                            @click="editItem = {{ json_encode($bag->only(['id','nama_pengirim','no_hp_pengirim','nama_penerima','no_hp_penerima','jenis_barang','keterangan','jumlah'])) }}; showForm = true">
                                            Edit
                                        </x-button>
                                        <form action="{{ route('baggage.destroy', [$trip, $bag]) }}" method="POST"
                                              onsubmit="return confirm('Hapus data bagasi ini?')">
                                            @csrf @method('DELETE')
                                            <x-button variant="ghost" size="sm" type="submit" class="text-red-500 hover:text-red-700">Hapus</x-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-card>
            <p class="text-xs text-slate-400">Total: {{ $trip->baggages->count() }} item bagasi</p>
        @endif

        {{-- Dialog --}}
        <div x-show="showForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div class="absolute inset-0 bg-black/40" @click="showForm = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md border border-slate-200" @click.stop
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold" x-text="editItem ? 'Edit Bagasi' : 'Tambah Bagasi'"></h3>
                    <button @click="showForm = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form :action="editItem ? '{{ url('trips/'.$trip->id.'/baggage') }}/' + editItem.id : '{{ route('baggage.store', $trip) }}'" method="POST" class="px-5 py-4 space-y-3">
                    @csrf
                    <template x-if="editItem"><input type="hidden" name="_method" value="PATCH"></template>

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Jenis Barang *</label>
                        <input type="text" name="jenis_barang" :value="editItem ? editItem.jenis_barang : ''" placeholder="Motor / Paket / Kardus" required
                               class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Jumlah *</label>
                        <input type="number" name="jumlah" :value="editItem ? editItem.jumlah : 1" min="1" required
                               class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Nama Pengirim *</label>
                            <input type="text" name="nama_pengirim" :value="editItem ? editItem.nama_pengirim : ''" required
                                   class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">HP Pengirim</label>
                            <input type="text" name="no_hp_pengirim" :value="editItem ? editItem.no_hp_pengirim : ''" placeholder="08xx..."
                                   class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Nama Penerima *</label>
                            <input type="text" name="nama_penerima" :value="editItem ? editItem.nama_penerima : ''" required
                                   class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">HP Penerima</label>
                            <input type="text" name="no_hp_penerima" :value="editItem ? editItem.no_hp_penerima : ''" placeholder="08xx..."
                                   class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Keterangan</label>
                        <textarea name="keterangan" rows="2" :value="editItem ? editItem.keterangan : ''" placeholder="Warna, plat nomor, dll..."
                                  class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 resize-none" x-text="editItem ? editItem.keterangan : ''"></textarea>
                    </div>

                    <div class="flex gap-2 pt-1">
                        <x-button type="submit">Simpan</x-button>
                        <x-button type="button" variant="ghost" @click="showForm = false">Batal</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
