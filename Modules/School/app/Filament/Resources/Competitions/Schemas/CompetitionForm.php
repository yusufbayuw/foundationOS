<?php

namespace Modules\School\Filament\Resources\Competitions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class CompetitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('name')
                        ->label(FilamentUi::field('name')),
                    TextInput::make('level')
                        ->label(FilamentUi::field('level')),
                    TextInput::make('held_at')
                        ->label(FilamentUi::field('held_at')),
                    TextInput::make('status')
                        ->label(FilamentUi::field('status')),
                ])
                ->columns(2),
        ]);
    }
}
