<?php

namespace Modules\Core\Filament\Support\Records;

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
                return (string) $value;
            }
        }

        if ($record->isRelation('user')) {
            $user = $record->getRelationValue('user');

            if ($user !== null && isset($user->name) && $user->name !== '') {
                return (string) $user->name;
            }
        }

        return (string) $record->getKey();
    }
}
