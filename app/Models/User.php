<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\User as CoreUser;

class User extends CoreUser
{
    public function getMorphClass(): string
    {
        return 'user';
    }

    public function passkeys(): HasMany
    {
        return $this->hasMany(UserPasskey::class);
    }
}
