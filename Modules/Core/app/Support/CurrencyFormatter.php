<?php

namespace Modules\Core\Support;

class CurrencyFormatter
{
    private const FORMATS = [
        'IDR' => ['symbol' => 'Rp', 'decimals' => 0, 'dec_point' => ',', 'thousands_sep' => '.', 'symbol_after' => false],
        'USD' => ['symbol' => '$', 'decimals' => 2, 'dec_point' => '.', 'thousands_sep' => ',', 'symbol_after' => false],
        'EUR' => ['symbol' => '€', 'decimals' => 2, 'dec_point' => ',', 'thousands_sep' => '.', 'symbol_after' => true],
        'SGD' => ['symbol' => 'S$', 'decimals' => 2, 'dec_point' => '.', 'thousands_sep' => ',', 'symbol_after' => false],
        'MYR' => ['symbol' => 'RM', 'decimals' => 2, 'dec_point' => '.', 'thousands_sep' => ',', 'symbol_after' => false],
    ];

    public static function format(float $amount, string $currency): string
    {
        $fmt = self::FORMATS[$currency] ?? self::FORMATS['IDR'];

        $formatted = number_format($amount, $fmt['decimals'], $fmt['dec_point'], $fmt['thousands_sep']);

        return $fmt['symbol_after']
            ? "{$formatted} {$fmt['symbol']}"
            : "{$fmt['symbol']} {$formatted}";
    }

    /**
     * @return array<string, mixed>
     */
    public static function options(): array
    {
        return [
            'IDR' => 'IDR — Indonesian Rupiah',
            'USD' => 'USD — US Dollar',
            'EUR' => 'EUR — Euro',
            'SGD' => 'SGD — Singapore Dollar',
            'MYR' => 'MYR — Malaysian Ringgit',
        ];
    }
}
