<?php

namespace Modules\Core\Providers;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Components\ComponentManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Support\FilamentUi;

/**
 * Registers a global configureUsing hook that applies FilamentUi::field()
 * as the default label for table columns and infolist entries that have no
 * explicit ->label() call. Explicit labels always win (they overwrite this
 * default after make() returns).
 *
 * Epic 4.1 — ROADMAPv2.md
 */
class FilamentTranslationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $manager = ComponentManager::resolve();

        $autoLabel = function (TextColumn|IconColumn|ImageColumn|TextEntry|IconEntry|ImageEntry $component): void {
            $component->label(
                fn () => FilamentUi::field($component->getName())
            );
        };

        // Table columns
        $manager->configureUsing(TextColumn::class, $autoLabel);
        $manager->configureUsing(IconColumn::class, $autoLabel);
        $manager->configureUsing(ImageColumn::class, $autoLabel);

        // Infolist entries
        $manager->configureUsing(TextEntry::class, $autoLabel);
        $manager->configureUsing(IconEntry::class, $autoLabel);
        $manager->configureUsing(ImageEntry::class, $autoLabel);
    }
}
