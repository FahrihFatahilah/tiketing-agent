<x-app-layout>
    <x-slot name="title">Armada</x-slot>

    <div x-data="{ open: false, edit: null }">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs text-zinc-400">{{ $buses->count() }} unit</p>
            <button @click="open = true; edit = null" class="btn-default btn-sm">+ Tambah</button>
        </div>

        <div class="card divide-y divide-zinc-100">
            @foreach($buses as $bus)
                <div class="flex items-center justify-between px-4 py-3">
                    <div>
                        <p class="text-sm font-semibold font-mono text-zinc-900">{{ $bus->nomor_lambung }}</p>
                        <p class="text-[11px] text-zinc-400">{{ $bus->busType->name }} · {{ $bus->busType->total_seat }} kursi</p>
                    </div>
                    <div class="flex gap-1">
                        <button @click="edit = {{ json_encode(['id'=>$bus->id,'nomor_lambung'=>$bus->nomor_lambung,'bus_type_id'=>$bus->bus_type_id]) }}; open = true"
                                class="btn-ghost btn-sm">Edit</button>
                        <form action="{{ route('admin.buses.destroy', $bus) }}" method="POST" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-ghost btn-sm text-red-500 hover:text-red-600 hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Sheet --}}
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center" style="display:none">
            <div class="absolute inset-0 bg-black/40" @click="open = false"></div>
            <div class="relative w-full max-w-md rounded-t-xl border-t border-zinc-200 bg-white shadow-xl" @click.stop
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0">
                <div class="flex justify-center pt-3"><div class="h-1 w-8 rounded-full bg-zinc-200"></div></div>
                <div class="flex items-center justify-between border-b border-zinc-100 px-4 py-3">
                    <p class="text-sm font-semibold" x-text="edit ? 'Edit Armada' : 'Tambah Armada'"></p>
                    <button @click="open = false" class="btn-ghost btn-icon">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form :action="edit ? '/admin/buses/'+edit.id : '{{ route('admin.buses.store') }}'" method="POST" class="px-4 pb-8 pt-4 space-y-3">
                    @csrf
                    <template x-if="edit"><input type="hidden" name="_method" value="PATCH"></template>
                    <div class="space-y-1.5">
                        <label class="label">Nomor Lambung</label>
                        <input type="text" name="nomor_lambung" :value="edit?.nomor_lambung ?? ''" placeholder="DR-001" required class="input">
                    </div>
                    <div class="space-y-1.5">
                        <label class="label">Tipe Bus</label>
                        <select name="bus_type_id" required class="input">
                            @foreach($busTypes as $type)
                                <option value="{{ $type->id }}" :selected="edit?.bus_type_id == {{ $type->id }}">
                                    {{ $type->name }} ({{ $type->total_seat }} kursi)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="btn-default flex-1">Simpan</button>
                        <button type="button" @click="open = false" class="btn-outline flex-1">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
