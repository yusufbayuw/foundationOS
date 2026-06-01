<?php

namespace Modules\Core\Policies\Concerns;

use Illuminate\Foundation\Auth\User as AuthUser;

trait AuthorizesPrint
{
    public function print(AuthUser $authUser, mixed $record): bool
    {
        if (method_exists($this, 'view')) {
            return $this->view($authUser, $record);
        }

        return false;
    }
}
