@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-400 bg-green-950/50 p-2 rounded']) }}>
        {{ $status }}
    </div>
@endif
