<?php

namespace App\Support;

use App\Events\PortalDataChanged;
use Illuminate\Support\Facades\Cache;

/**
 * Global portal data revision counter + broadcast tick so guest, host, and admin UIs stay aligned.
 */
final class PortalSync
{
    private const CACHE_KEY = 'portal:sync:version';

    public static function version(): int
    {
        return (int) Cache::get(self::CACHE_KEY, 0);
    }

    public static function bump(?string $source = null): int
    {
        $version = (int) Cache::increment(self::CACHE_KEY);

        if (config('broadcasting.default') === 'null') {
            return $version;
        }

        try {
            broadcast(new PortalDataChanged($version, $source));
        } catch (\Throwable) {
            // Reverb / Pusher not running or misconfigured — polling still converges.
        }

        return $version;
    }
}
