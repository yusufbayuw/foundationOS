<?php

namespace Modules\ItOps\Contracts;

interface MikroTikClient
{
    /**
     * @return array<string, mixed>
     */
    public function listActiveSessions(): array;

    public function disconnectUser(string $username): bool;
}
