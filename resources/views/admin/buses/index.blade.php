<x-app-layout>
    <x-slot name="title">Manajemen Armada</x-slot>

    <div class="space-y-5" x-data="{ showForm: false, editItem: null }">
        <div class="flex justify-between items-center">
            <p class="text-sm text-slate-500">{{ $buses->count() }} unit armada</p>
            <x-button @click="showForm = true; editItem = null">+ Tambah Armada</x-button>
        </div>

        <x-card>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">No. Lambung</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tipe</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Kapasitas</th>
                        <th class="px-4 py-3 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($buses as $bus)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium font-mono">{{ $bus->nomor_lambung }}</td>
                            <td class="px-4 py-3"><x-badge>{{ $bus->busType->name }}</x-badge></td>
                            <td class="px-4 py-3 text-slate-500">{{ $bus->busType->total_seat }} kursi</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex gap-1 justify-end">
                                    <x-button variant="ghost" size="sm"
                                        @click="editItem = {{ json_encode(['id' => $bus->id, 'nomor_lambung' => $bus->nomor_lambung, 'bus_type_id' => $bus->bus_type_id]) }}; showForm = true">
                                        Edit
                                    </x-button>
                                    <form action="{{ route('admin.buses.destroy', $bus) }}" method="POST"
                                          onsubmit="return confirm('Hapus armada ini?')">
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

        {{-- Dialog --}}
        <div x-show="showForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div class="absolute inset-0 bg-black/40" @click="showForm = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md border border-slate-200" @click.stop
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold" x-text="editItem ? 'Edit Armada' : 'Tambah Armada'"></h3>
                    <button @click="showForm = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form :action="editItem ? '/admin/buses/' + editItem.id : '{{ route('admin.buses.store') }}'" method="POST" class="px-5 py-4 space-y-3">
                    @csrf
                    <template x-if="editItem"><input type="hidden" name="_method" value="PATCH"></template>

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Nomor Lambung</label>
                        <input type="text" name="nomor_lambung" :value="editItem ? editItem.nomor_lambung : ''" placeholder="DR-001" required
                               class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Tipe Bus</label>
                        <select name="bus_type_id" required
                                class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                            @foreach($busTypes as $type)
                                <option value="{{ $type->id }}" :selected="editItem && editItem.bus_type_id == {{ $type->id }}">
                                    {{ $type->name }} ({{ $type->total_seat }} kursi)
                                </option>
                            @endforeach
                        </select>
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
