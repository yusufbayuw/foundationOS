<?php

namespace App\Models;

use Modules\Core\Models\User as CoreUser;

class User extends CoreUser
{
    public function getMorphClass(): string
    {
        return 'user';
    }
}
