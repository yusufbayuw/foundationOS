<?php

namespace Modules\Ai\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Ai\Models\AiPromptTemplate;
use Modules\Ai\Policies\AiPromptTemplatePolicy;

class AiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(AiPromptTemplate::class, AiPromptTemplatePolicy::class);
    }
}
