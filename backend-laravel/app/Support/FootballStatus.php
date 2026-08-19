<?php

namespace App\Support;

final class FootballStatus
{
    public static function canonical(?string $raw): string
    {
        return match (strtoupper(trim((string) $raw))) {
            '1H', '2H', 'ET', 'BT', 'P', 'LIVE', 'IN_PLAY' => 'live',
            'HT', 'PAUSED' => 'halftime',
            'FT', 'AET', 'PEN', 'AWD', 'WO', 'FINISHED' => 'finished',
            'PST', 'POSTPONED' => 'postponed',
            'CANC', 'CANCELLED' => 'cancelled',
            'ABD', 'ABANDONED' => 'abandoned',
            'SUSP', 'INT', 'SUSPENDED' => 'suspended',
            default => 'scheduled',
        };
    }

    public static function isLive(?string $raw): bool
    {
        return in_array(self::canonical($raw), ['live', 'halftime'], true);
    }
}
