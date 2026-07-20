<?php

namespace Modules\Member\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class MemberServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Member';

    protected string $nameLower = 'member';

    protected array $providers = [
        RouteServiceProvider::class,
    ];
}
