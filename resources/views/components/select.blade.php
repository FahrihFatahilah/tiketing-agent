@props(['label' => null])
<div class="space-y-1">
    @if($label)
        <label class="block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    <select {{ $attributes->merge(['class' => 'w-full h-9 px-3 text-sm border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition']) }}>
        {{ $slot }}
    </select>
</div>
