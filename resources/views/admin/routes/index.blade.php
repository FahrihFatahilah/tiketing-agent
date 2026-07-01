<x-app-layout>
    <x-slot name="title">Manajemen Rute</x-slot>

    <div class="space-y-5" x-data="{ showForm: false, editItem: null }">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <p class="text-sm text-slate-500">{{ $routes->count() }} rute</p>
            <x-button @click="showForm = true; editItem = null">+ Tambah Rute</x-button>
        </div>

        <x-card class="overflow-x-auto">
            <table class="w-full text-sm min-w-[500px]">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama Rute</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Asal</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tujuan</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Jadwal</th>
                        <th class="px-4 py-3 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($routes as $route)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $route->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $route->origin }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $route->destination }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $route->schedules_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex gap-1 justify-end">
                                    <x-button variant="ghost" size="sm"
                                        @click="editItem = {{ json_encode(['id' => $route->id, 'name' => $route->name, 'origin' => $route->origin, 'destination' => $route->destination]) }}; showForm = true">
                                        Edit
                                    </x-button>
                                    <form action="{{ route('admin.routes.destroy', $route) }}" method="POST"
                                          onsubmit="return confirm('Hapus rute ini?')">
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
                    <h3 class="text-sm font-semibold" x-text="editItem ? 'Edit Rute' : 'Tambah Rute'"></h3>
                    <button @click="showForm = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form :action="editItem ? '/admin/routes/' + editItem.id : '{{ route('admin.routes.store') }}'" method="POST" class="px-5 py-4 space-y-3">
                    @csrf
                    <template x-if="editItem"><input type="hidden" name="_method" value="PATCH"></template>

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Nama Rute</label>
                        <input type="text" name="name" :value="editItem ? editItem.name : ''" placeholder="Mataram - Bima" required
                               class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Asal</label>
                        <input type="text" name="origin" :value="editItem ? editItem.origin : ''" placeholder="Mataram" required
                               class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Tujuan</label>
                        <input type="text" name="destination" :value="editItem ? editItem.destination : ''" placeholder="Bima" required
                               class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
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
