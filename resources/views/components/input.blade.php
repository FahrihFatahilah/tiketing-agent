@props(['label' => null, 'error' => null])
<div class="space-y-1">
    @if($label)
        <label class="block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    <input {{ $attributes->merge(['class' => 'w-full h-9 px-3 text-sm border border-slate-300 rounded-md bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition']) }}>
    @if($error)
        <p class="text-xs text-red-600">{{ $error }}</p>
    @endif
</div>
