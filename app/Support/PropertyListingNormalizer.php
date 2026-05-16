<?php

namespace App\Support;

use Illuminate\Support\Str;

final class PropertyListingNormalizer
{
    /**
     * @param  array<int, mixed>|null  $input
     * @return array<int>|null
     */
    public static function amenityIds(?array $input): ?array
    {
        $ids = collect($input ?? [])
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        return count($ids) ? $ids : null;
    }

    public static function checkTime(?string $value, string $default): string
    {
        $raw = $value ?: $default;
        $raw = strlen($raw) === 5 ? $raw.':00' : $raw;

        return $raw;
    }

    public static function cancellationBody(?string $preset, ?string $customText): string
    {
        return match ($preset) {
            'flexible', 'moderate', 'firm' => __('frontend.host_listing.cancellation_presets.'.$preset),
            'custom' => $customText ?? '',
            default => __('frontend.host_listing.cancellation_presets.moderate'),
        };
    }

    /**
     * @param  array{name: string, description?: string|null, meta_title?: string|null, meta_description?: string|null}  $validated
     * @return array{meta_title: string, meta_description: string|null}
     */
    public static function metaFields(array $validated): array
    {
        $plainDesc = strip_tags((string) ($validated['description'] ?? ''));

        return [
            'meta_title' => ($validated['meta_title'] ?? '') !== ''
                ? $validated['meta_title']
                : Str::limit($validated['name'], 60),
            'meta_description' => ($validated['meta_description'] ?? '') !== ''
                ? $validated['meta_description']
                : ($plainDesc !== '' ? Str::limit($plainDesc, 155) : null),
        ];
    }
}
