<?php

namespace Modules\Transport\Filament\Resources\BoardingLogs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class BoardingLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('organization_id')
                        ->label(FilamentUi::field('organization_id'))
                        ->numeric(),
                    TextInput::make('code')
                        ->label(FilamentUi::field('code')),
                    TextInput::make('name')
                        ->label(FilamentUi::field('name')),
                    TextInput::make('status')
                        ->label(FilamentUi::field('status')),
                    Textarea::make('description')
                        ->label(FilamentUi::field('description'))
                        ->columnSpanFull(),
                    Textarea::make('meta')
                        ->label(FilamentUi::field('meta'))
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
