@props([
    'variant' => 'default',
])

@php
    $base = 'elapse-wordmark';
    $variantClass = match ($variant) {
        'footer' => ' elapse-wordmark--footer',
        'admin' => ' elapse-wordmark--admin',
        default => '',
    };
@endphp
<span {{ $attributes->merge(['class' => trim($base.$variantClass)]) }}>{{ config('app.name', 'Elapse') }}</span>
