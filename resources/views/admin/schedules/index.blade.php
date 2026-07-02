<x-app-layout>
    <x-slot name="title">Jadwal</x-slot>

    <div class="space-y-4" x-data="{ showForm: false, editItem: null }">

        <div class="flex items-center justify-between">
            <p class="text-xs text-slate-400 font-medium">{{ $schedules->count() }} jadwal</p>
            <button @click="showForm = true; editItem = null"
                    class="h-9 px-4 bg-indigo-600 text-white text-xs font-bold rounded-2xl active:scale-[.98] transition-transform flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah
            </button>
        </div>

        <div class="space-y-2">
            @foreach($schedules as $schedule)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-purple-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-bold text-slate-900">{{ $schedule->label }}</p>
                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full
                                {{ $schedule->aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $schedule->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400">{{ $schedule->route->name }}</p>
                    </div>
                    <div class="text-right shrink-0 mr-1">
                        <p class="text-sm font-bold font-mono text-indigo-600">{{ \Carbon\Carbon::parse($schedule->jam_berangkat)->format('H:i') }}</p>
                    </div>
                    <div class="flex gap-1.5 shrink-0">
                        <button @click="editItem = {{ json_encode(['id' => $schedule->id, 'route_id' => $schedule->route_id, 'label' => $schedule->label, 'jam_berangkat' => \Carbon\Carbon::parse($schedule->jam_berangkat)->format('H:i'), 'aktif' => $schedule->aktif]) }}; showForm = true"
                                class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 active:bg-slate-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center text-red-500 active:bg-red-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Bottom Sheet --}}
        <div x-show="showForm" x-cloak class="fixed inset-0 z-50 flex items-end justify-center" style="display:none">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showForm = false"></div>
            <div class="relative bg-white rounded-t-3xl w-full max-w-md shadow-2xl" @click.stop
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0">
                <div class="flex justify-center pt-3 pb-1"><div class="w-10 h-1 bg-slate-200 rounded-full"></div></div>
                <div class="px-5 py-3 flex items-center justify-between">
                    <h3 class="text-base font-bold" x-text="editItem ? 'Edit Jadwal' : 'Tambah Jadwal'"></h3>
                    <button @click="showForm = false" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form :action="editItem ? '/admin/schedules/' + editItem.id : '{{ route('admin.schedules.store') }}'" method="POST" class="px-5 pb-8 space-y-3">
                    @csrf
                    <template x-if="editItem"><input type="hidden" name="_method" value="PATCH"></template>
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Rute</label>
                        <select name="route_id" required class="mt-1 w-full h-11 px-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition appearance-none">
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" :selected="editItem && editItem.route_id == {{ $route->id }}">{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Label</label>
                            <input type="text" name="label" :value="editItem ? editItem.label : ''" placeholder="Pagi 09:00" required
                                   class="mt-1 w-full h-11 px-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Jam</label>
                            <input type="time" name="jam_berangkat" :value="editItem ? editItem.jam_berangkat : ''" required
                                   class="mt-1 w-full h-11 px-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                    </div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="aktif" value="1" id="aktif_check" :checked="!editItem || editItem.aktif"
                               class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-slate-700">Jadwal Aktif</span>
                    </label>
                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="flex-1 h-11 bg-indigo-600 text-white text-sm font-bold rounded-2xl active:scale-[.98] transition-transform">Simpan</button>
                        <button type="button" @click="showForm = false" class="flex-1 h-11 bg-slate-100 text-slate-600 text-sm font-bold rounded-2xl active:scale-[.98] transition-transform">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
