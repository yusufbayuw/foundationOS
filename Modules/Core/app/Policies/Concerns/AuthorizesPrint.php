<?php

namespace Modules\Core\Policies\Concerns;

use Illuminate\Foundation\Auth\User as AuthUser;

/**
 * @method bool view(AuthUser $authUser, mixed $record)
 */
trait AuthorizesPrint
{
    public function print(AuthUser $authUser, mixed $record): bool
    {
        return $this->view($authUser, $record);
    }
}
