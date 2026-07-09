<?php

namespace App\Enums;

enum RegimeTributario: string
{
    case LUCRO_REAL       = 'LUCRO_REAL';
    case SIMPLES_NACIONAL = 'SIMPLES_NACIONAL';

    public function label(): string
    {
        return match($this) {
            self::LUCRO_REAL       => 'Lucro Real',
            self::SIMPLES_NACIONAL => 'Simples Nacional',
        };
    }
}
