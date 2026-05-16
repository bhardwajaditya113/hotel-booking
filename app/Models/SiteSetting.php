<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Single row used by admin site settings screen (creates defaults when missing).
     */
    public static function forAdminEdit(): self
    {
        $site = static::query()->first();

        if ($site !== null) {
            return $site;
        }

        return static::query()->create([
            'logo' => null,
            'phone' => '',
            'address' => '',
            'email' => '',
            'facebook' => '',
            'twitter' => '',
            'copyright' => '',
        ]);
    }
}
