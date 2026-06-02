<?php

namespace Modules\Helpdesk\Filament\Resources\TicketCategories\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class TicketCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                        TextInput::make('response_hours')
                            ->label(FilamentUi::field('response_hours'))
                            ->numeric()
                            ->default(4)
                            ->required(),
                        TextInput::make('resolution_hours')
                            ->label(FilamentUi::field('resolution_hours'))
                            ->numeric()
                            ->default(24)
                            ->required(),
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
