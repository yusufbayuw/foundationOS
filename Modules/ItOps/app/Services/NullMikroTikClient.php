<?php

namespace Modules\ItOps\Services;

use Modules\ItOps\Contracts\MikroTikClient;

class NullMikroTikClient implements MikroTikClient
{
    public function listActiveSessions(): array
    {
        return [];
    }

    public function disconnectUser(string $username): bool
    {
        return false;
    }
}
