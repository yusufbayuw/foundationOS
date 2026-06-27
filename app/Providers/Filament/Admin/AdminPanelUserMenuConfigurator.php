<?php

namespace App\Providers\Filament\Admin;

use Filament\Actions\Action;
use Filament\Panel;
use Modules\Core\Support\FilamentUi;

class AdminPanelUserMenuConfigurator
{
    public function configure(Panel $panel): Panel
    {
        return $panel->userMenuItems([
            Action::make('switch_to_english')
                ->label('🇬🇧 English')
                ->icon('heroicon-o-language')
                ->url(fn () => route('locale.switch', 'en'))
                ->visible(fn () => FilamentUi::isIndonesian()),
            Action::make('switch_to_indonesian')
                ->label('🇮🇩 Indonesia')
                ->icon('heroicon-o-language')
                ->url(fn () => route('locale.switch', 'id'))
                ->visible(fn () => ! FilamentUi::isIndonesian()),
        ]);
    }
}
