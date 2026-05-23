<?php

namespace Modules\Counseling\Providers;

use Illuminate\Support\Facades\Gate;
use Modules\Counseling\Models\CounselingNote;
use Modules\Counseling\Policies\CounselingNotePolicy;
use Nwidart\Modules\Support\ModuleServiceProvider;

class CounselingServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Counseling';

    protected string $nameLower = 'counseling';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        Gate::policy(CounselingNote::class, CounselingNotePolicy::class);
    }
}
