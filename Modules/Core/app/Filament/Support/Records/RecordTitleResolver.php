<?php

namespace Modules\Core\Filament\Support\Records;

use App\Support\TypedValue;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class RecordTitleResolver
{
    public static function resolve(?Model $record): string|Htmlable|null
    {
        if (! $record) {
            return null;
        }

        $candidates = [
            'name', 'full_name', 'title', 'code',
            'entry_number', 'invoice_number', 'payment_number',
            'employee_number', 'student_number', 'registration_number',
            'nis', 'nip', 'subject_label',
        ];

        foreach ($candidates as $attr) {
            $value = $record->getAttribute($attr);
            if ($value !== null && $value !== '') {
                return TypedValue::string($value);
            }
        }

        if ($record->isRelation('user')) {
            $user = $record->getRelationValue('user');
            $userName = data_get($user, 'name');

            if (is_string($userName) && $userName !== '') {
                return $userName;
            }
        }

        return TypedValue::string($record->getKey());
    }
}
