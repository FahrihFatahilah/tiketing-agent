<x-app-layout>
    <x-slot name="title">Armada</x-slot>

    <div class="space-y-4" x-data="{ showForm: false, editItem: null }">

        <div class="flex items-center justify-between">
            <p class="text-xs text-slate-400 font-medium">{{ $buses->count() }} unit terdaftar</p>
            <button @click="showForm = true; editItem = null"
                    class="h-9 px-4 bg-indigo-600 text-white text-xs font-bold rounded-2xl active:scale-[.98] transition-transform flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah
            </button>
        </div>

        <div class="space-y-2">
            @foreach($buses as $bus)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 font-mono">{{ $bus->nomor_lambung }}</p>
                        <p class="text-xs text-slate-400">{{ $bus->busType->name }} · {{ $bus->busType->total_seat }} kursi</p>
                    </div>
                    <div class="flex gap-1.5 shrink-0">
                        <button @click="editItem = {{ json_encode(['id' => $bus->id, 'nomor_lambung' => $bus->nomor_lambung, 'bus_type_id' => $bus->bus_type_id]) }}; showForm = true"
                                class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 active:bg-slate-200 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form action="{{ route('admin.buses.destroy', $bus) }}" method="POST"
                              onsubmit="return confirm('Hapus armada ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center text-red-500 active:bg-red-100 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Bottom Sheet Form --}}
        <div x-show="showForm" x-cloak class="fixed inset-0 z-50 flex items-end justify-center" style="display:none">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showForm = false"></div>
            <div class="relative bg-white rounded-t-3xl w-full max-w-md shadow-2xl"
                 @click.stop
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0">
                <div class="flex justify-center pt-3 pb-1">
                    <div class="w-10 h-1 bg-slate-200 rounded-full"></div>
                </div>
                <div class="px-5 py-3 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900" x-text="editItem ? 'Edit Armada' : 'Tambah Armada'"></h3>
                    <button @click="showForm = false" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form :action="editItem ? '/admin/buses/' + editItem.id : '{{ route('admin.buses.store') }}'" method="POST" class="px-5 pb-8 space-y-3">
                    @csrf
                    <template x-if="editItem"><input type="hidden" name="_method" value="PATCH"></template>
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Nomor Lambung</label>
                        <input type="text" name="nomor_lambung" :value="editItem ? editItem.nomor_lambung : ''" placeholder="DR-001" required
                               class="mt-1 w-full h-11 px-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Tipe Bus</label>
                        <select name="bus_type_id" required
                                class="mt-1 w-full h-11 px-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition appearance-none">
                            @foreach($busTypes as $type)
                                <option value="{{ $type->id }}" :selected="editItem && editItem.bus_type_id == {{ $type->id }}">
                                    {{ $type->name }} ({{ $type->total_seat }} kursi)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="flex-1 h-11 bg-indigo-600 text-white text-sm font-bold rounded-2xl active:scale-[.98] transition-transform">Simpan</button>
                        <button type="button" @click="showForm = false" class="flex-1 h-11 bg-slate-100 text-slate-600 text-sm font-bold rounded-2xl active:scale-[.98] transition-transform">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
