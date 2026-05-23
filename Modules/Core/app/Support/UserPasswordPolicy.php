<?php

namespace Modules\Core\Support;

use Illuminate\Validation\Rules\Password;

class UserPasswordPolicy
{
    public static function rules(): array
    {
        return [
            'required',
            'string',
            Password::min(10)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols(),
        ];
    }

    public static function mustChangeAfterDays(): int
    {
        return (int) config('auth.password_change_days', 90);
    }
}
