<?php

namespace Modules\Facility\Filament\Resources\Rooms\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TenantField::organizationSelect(),
                    TextInput::make('code')
                        ->label(FilamentUi::field('code'))
                        ->required(),
                    TextInput::make('name')
                        ->label(FilamentUi::field('name'))
                        ->required(),
                    TextInput::make('status')
                        ->label(FilamentUi::field('status'))
                        ->default('active'),
                    Toggle::make('is_bookable')
                        ->label(FilamentUi::field('is_bookable'))
                        ->default(true),
                    Toggle::make('is_rentable')
                        ->label(FilamentUi::field('is_rentable'))
                        ->default(false),
                    Textarea::make('description')
                        ->label(FilamentUi::field('description'))
                        ->columnSpanFull(),
                    KeyValue::make('meta')
                        ->label(FilamentUi::field('meta'))
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
