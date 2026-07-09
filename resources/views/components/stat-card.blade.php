@props(['label', 'value', 'description' => null])
<div class="card p-4">
    <p class="text-xs text-surface-500 font-medium uppercase tracking-wider">{{ $label }}</p>
    <p class="text-xl font-bold text-surface-800 mt-1">{{ $value }}</p>
    @if($description)
        <p class="text-xs text-surface-400 mt-1">{{ $description }}</p>
    @endif
</div>
