<?php

namespace Modules\Core\Support;

use App\Support\TypedValue;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;

class UserPasswordPolicy
{
    /**
     * @return list<ValidationRule|string>
     */
    public static function rules(): array
    {
        $passwordRule = Password::min(10)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        /** @var list<ValidationRule|string> $rules */
        $rules = ['required', 'string', $passwordRule];

        return $rules;
    }

    public static function mustChangeAfterDays(): int
    {
        return TypedValue::int(config('auth.password_change_days'), 90);
    }
}
