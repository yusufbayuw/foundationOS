<?php

namespace Modules\Core\Filament\Resources\TenantModules\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class TenantModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('module_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('module_id'))
                    ->relationship('module', 'name')
                    ->required(),
                Toggle::make('is_enabled')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_enabled'))
                    ->required(),
                DateTimePicker::make('enabled_at'),
                DateTimePicker::make('disabled_at'),
                Textarea::make('settings')
                    ->label(\Modules\Core\Support\FilamentUi::field('settings'))
                    ->columnSpanFull(),
            ]);
    }
}
