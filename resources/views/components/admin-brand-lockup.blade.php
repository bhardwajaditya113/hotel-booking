@props([
    /** sm: toolbar mobile | md: sidebar | lg: login hero */
    'size' => 'md',
])

@php
    $sizeClass = match ($size) {
        'sm' => 'nx-admin-brand-lockup--sm',
        'lg' => 'nx-admin-brand-lockup--lg',
        default => 'nx-admin-brand-lockup--md',
    };
@endphp

<span {{ $attributes->merge(['class' => trim('nx-admin-brand-lockup '.$sizeClass)]) }}>
    <span class="nx-admin-brand-mark" aria-hidden="true">
        <img src="{{ asset('frontend/assets/img/elapse-mark.svg') }}" alt="" width="120" height="120" decoding="async" />
    </span>
    <span class="nx-admin-brand-copy">
        <span class="nx-admin-brand-name">{{ config('app.name', 'Elapse') }}</span>
        @unless ($size === 'sm')
            <span class="nx-admin-brand-tag">Admin console</span>
        @endunless
    </span>
</span>
