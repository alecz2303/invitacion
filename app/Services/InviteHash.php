<?php

namespace App\Services;

use App\Models\Tenant;
use Hashids\Hashids;

class InviteHash
{
    public static function make(Tenant $tenant): Hashids
    {
        $salt = config('app.key').'|partyx|'.$tenant->id.'|'.$tenant->slug;
        return new Hashids($salt, 8);
    }

    public static function encode(Tenant $tenant, int $inviteId): string
    {
        return self::make($tenant)->encode($inviteId);
    }

    public static function decode(Tenant $tenant, string $hash): ?int
    {
        $decoded = self::make($tenant)->decode($hash);
        return $decoded[0] ?? null;
    }
}
