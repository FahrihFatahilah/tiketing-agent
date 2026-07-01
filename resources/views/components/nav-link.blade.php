@props(['href', 'active' => false])
<a href="{{ $href }}"
   class="flex items-center gap-2.5 px-2 py-2 rounded-md text-sm transition-colors
          {{ $active ? 'bg-slate-100 text-slate-900 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
    {{ $slot }}
</a>
