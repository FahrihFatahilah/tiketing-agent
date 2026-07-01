@props(['id', 'title' => ''])
{{-- Usage: wrap trigger in x-data, use @click="$dispatch('open-dialog', {id: '{{ $id }}'})" --}}
<div
    x-data="{ open: false }"
    x-on:open-dialog.window="if ($event.detail.id === '{{ $id }}') open = true"
    x-on:close-dialog.window="if ($event.detail.id === '{{ $id }}') open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display:none">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40" @click="open = false"></div>

    {{-- Panel --}}
    <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md border border-slate-200"
         @click.stop
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
            <button @click="open = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-5 py-4">
            {{ $slot }}
        </div>
    </div>
</div>
