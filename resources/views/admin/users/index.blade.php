<x-app-layout>
    <x-slot name="title">Manajemen Pengguna</x-slot>

    <div class="space-y-5" x-data="{ showForm: false, editUser: null }">
        <div class="flex justify-between items-center">
            <p class="text-sm text-slate-500">{{ $users->count() }} pengguna terdaftar</p>
            <x-button @click="showForm = true; editUser = null">+ Tambah Pengguna</x-button>
        </div>

        <x-card>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Email</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Role</th>
                        <th class="px-4 py-3 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @php $role = $user->getRoleNames()->first(); @endphp
                                <x-badge variant="{{ $role === 'admin' ? 'danger' : ($role === 'pengurus' ? 'info' : 'default') }}">
                                    {{ $role ?? '—' }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex gap-1 justify-end">
                                    <x-button variant="ghost" size="sm"
                                        @click="editUser = {{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->getRoleNames()->first()]) }}; showForm = true">
                                        Edit
                                    </x-button>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Hapus pengguna ini?')">
                                            @csrf @method('DELETE')
                                            <x-button variant="ghost" size="sm" type="submit" class="text-red-500 hover:text-red-700">Hapus</x-button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>

        {{-- Add/Edit Dialog --}}
        <div x-show="showForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div class="absolute inset-0 bg-black/40" @click="showForm = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md border border-slate-200" @click.stop
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold" x-text="editUser ? 'Edit Pengguna' : 'Tambah Pengguna'"></h3>
                    <button @click="showForm = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form :action="editUser ? '/admin/users/' + editUser.id : '{{ route('admin.users.store') }}'" method="POST" class="px-5 py-4 space-y-3">
                    @csrf
                    <template x-if="editUser"><input type="hidden" name="_method" value="PATCH"></template>

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Nama</label>
                        <input type="text" name="name" :value="editUser ? editUser.name : ''" required
                               class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" name="email" :value="editUser ? editUser.email : ''" required
                               class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Password</label>
                        <input type="password" name="password" :placeholder="editUser ? 'Kosongkan jika tidak diubah' : 'Min. 8 karakter'"
                               class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Role</label>
                        <select name="role" required
                                class="w-full h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" :selected="editUser && editUser.role === '{{ $role->name }}'">{{ ucfirst($role->name) }}</option>
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
