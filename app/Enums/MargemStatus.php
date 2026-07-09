<?php

namespace App\Enums;

enum MargemStatus: string
{
    case VERDE    = 'VERDE';
    case AMARELO  = 'AMARELO';
    case VERMELHO = 'VERMELHO';

    public function label(): string
    {
        return match($this) {
            self::VERDE    => 'Margem positiva',
            self::AMARELO  => 'Sem margem',
            self::VERMELHO => 'Margem negativa',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::VERDE    => 'badge-verde',
            self::AMARELO  => 'badge-amarelo',
            self::VERMELHO => 'badge-vermelho',
        };
    }
}
