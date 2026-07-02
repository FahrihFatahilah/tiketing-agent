<x-app-layout>
    <x-slot name="title">Jadwal</x-slot>

    <div x-data="{ open: false, edit: null }">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs text-zinc-400">{{ $schedules->count() }} jadwal</p>
            <button @click="open = true; edit = null" class="btn-default btn-sm">+ Tambah</button>
        </div>

        <div class="card divide-y divide-zinc-100">
            @foreach($schedules as $schedule)
                <div class="flex items-center justify-between px-4 py-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-zinc-900">{{ $schedule->label }}</p>
                            <span class="{{ $schedule->aktif ? 'badge-success' : 'badge-secondary' }} badge">
                                {{ $schedule->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-zinc-400">{{ $schedule->route->name }} · {{ \Carbon\Carbon::parse($schedule->jam_berangkat)->format('H:i') }}</p>
                    </div>
                    <div class="flex gap-1">
                        <button @click="edit = {{ json_encode(['id'=>$schedule->id,'route_id'=>$schedule->route_id,'label'=>$schedule->label,'jam_berangkat'=>\Carbon\Carbon::parse($schedule->jam_berangkat)->format('H:i'),'aktif'=>$schedule->aktif]) }}; open = true"
                                class="btn-ghost btn-sm">Edit</button>
                        <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-ghost btn-sm text-red-500 hover:text-red-600 hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center" style="display:none">
            <div class="absolute inset-0 bg-black/40" @click="open = false"></div>
            <div class="relative w-full max-w-md rounded-t-xl border-t border-zinc-200 bg-white shadow-xl" @click.stop
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0">
                <div class="flex justify-center pt-3"><div class="h-1 w-8 rounded-full bg-zinc-200"></div></div>
                <div class="flex items-center justify-between border-b border-zinc-100 px-4 py-3">
                    <p class="text-sm font-semibold" x-text="edit ? 'Edit Jadwal' : 'Tambah Jadwal'"></p>
                    <button @click="open = false" class="btn-ghost btn-icon">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form :action="edit ? '/admin/schedules/'+edit.id : '{{ route('admin.schedules.store') }}'" method="POST" class="px-4 pb-8 pt-4 space-y-3">
                    @csrf
                    <template x-if="edit"><input type="hidden" name="_method" value="PATCH"></template>
                    <div class="space-y-1.5">
                        <label class="label">Rute</label>
                        <select name="route_id" required class="input">
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" :selected="edit?.route_id == {{ $route->id }}">{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="label">Label</label>
                            <input type="text" name="label" :value="edit?.label ?? ''" placeholder="Pagi 09:00" required class="input">
                        </div>
                        <div class="space-y-1.5">
                            <label class="label">Jam</label>
                            <input type="time" name="jam_berangkat" :value="edit?.jam_berangkat ?? ''" required class="input">
                        </div>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="aktif" value="1" :checked="!edit || edit.aktif"
                               class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                        <span class="text-sm text-zinc-700">Aktif</span>
                    </label>
                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="btn-default flex-1">Simpan</button>
                        <button type="button" @click="open = false" class="btn-outline flex-1">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
