<?php

namespace Modules\Core\Support\Filament;

use Filament\Navigation\NavigationGroup;
use Illuminate\Support\Collection;
use Modules\Core\Support\FilamentUi;
use Nwidart\Modules\Facades\Module;

class EnabledModuleRegistry
{
    /**
     * @return Collection<int, \Nwidart\Modules\Module>
     */
    public function all(): Collection
    {
        return once(fn (): Collection => collect(Module::allEnabled()));
    }

    /**
     * @return list<NavigationGroup>
     */
    public function navigationGroups(): array
    {
        return $this->all()
            ->map(fn ($module) => NavigationGroup::make()->label(FilamentUi::module($module->getName())))
            ->values()
            ->all();
    }
}
