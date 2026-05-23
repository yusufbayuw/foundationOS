<?php

namespace Modules\Legal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Legal\Models\Contract;

class ContractExpiringSoon
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Contract $contract,
    ) {}
}
