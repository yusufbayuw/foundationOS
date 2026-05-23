<?php

namespace Modules\EOffice\Services;

use Modules\EOffice\Models\Letter;
use Modules\EOffice\Models\LetterTemplate;

class LetterNumberingService
{
    public function assignNextNumber(Letter $letter, ?LetterTemplate $template = null): string
    {
        $format = data_get($template?->meta, 'number_format', '{seq}/{type}/{month}/{year}');
        $tenantId = $letter->tenant_id;
        $year = now()->year;
        $month = now()->format('m');

        $sequence = Letter::query()
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;

        $number = str_replace(
            ['{seq}', '{type}', '{month}', '{year}'],
            [str_pad((string) $sequence, 4, '0', STR_PAD_LEFT), $letter->direction ?? 'OUT', $month, (string) $year],
            $format,
        );

        $letter->forceFill(['letter_number' => $number])->save();

        return $number;
    }
}
