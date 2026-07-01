@props(['class' => ''])
<div {{ $attributes->merge(['class' => 'bg-white border border-slate-200 rounded-lg shadow-sm ' . $class]) }}>
    {{ $slot }}
</div>
