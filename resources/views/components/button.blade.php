@props(['variant' => 'default', 'size' => 'default', 'type' => 'button'])

@php
$base = 'inline-flex items-center justify-center gap-1.5 font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:pointer-events-none';

$variants = [
    'default'     => 'bg-slate-900 text-white hover:bg-slate-800 focus:ring-slate-900',
    'outline'     => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus:ring-slate-400',
    'ghost'       => 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:ring-slate-400',
    'destructive' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-600',
    'success'     => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-600',
];

$sizes = [
    'default' => 'h-9 px-4 text-sm',
    'sm'      => 'h-7 px-3 text-xs',
    'lg'      => 'h-10 px-5 text-sm',
    'icon'    => 'h-8 w-8 text-sm',
];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "$base {$variants[$variant]} {$sizes[$size]}"]) }}>
    {{ $slot }}
</button>
