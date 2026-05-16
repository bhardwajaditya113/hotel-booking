<?php

namespace App\Support;

/**
 * Resolves stored media paths: remote URLs, upload/* relative paths, or bare filenames under a folder.
 */
final class MediaUrl
{
    public const FALLBACK_LEISURE = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1600&q=85';

    public static function resolve(?string $path, string $bareFilenameDirectory = ''): string
    {
        $path = $path === null ? '' : trim($path);
        if ($path === '') {
            return self::FALLBACK_LEISURE;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, '//')) {
            return 'https:'.$path;
        }
        if (str_starts_with($path, 'upload/')) {
            return asset($path);
        }
        if ($bareFilenameDirectory !== '' && ! str_contains($path, '/')) {
            return asset(rtrim($bareFilenameDirectory, '/').'/'.$path);
        }

        return asset($path);
    }

    /**
     * Path relative to public/ for unlinking local files, or null when remote / unknown.
     */
    public static function publicDiskPathForUnlink(?string $path, string $bareFilenameDirectory): ?string
    {
        if (! $path) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return null;
        }
        if (str_starts_with($path, 'upload/')) {
            return $path;
        }
        if (! str_contains($path, '/')) {
            return rtrim($bareFilenameDirectory, '/').'/'.$path;
        }

        return null;
    }
}
