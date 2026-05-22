<?php

namespace Modules\Campus\Filament\Resources\Faculties\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class FacultyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Faculty Details'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code')),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('short_name')
                            ->label(FilamentUi::field('short_name')),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Contact Information'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('office_phone')
                            ->label(FilamentUi::field('office_phone'))
                            ->tel(),
                        TextInput::make('office_email')
                            ->label(FilamentUi::field('office_email'))
                            ->email(),
                    ]),

                Section::make(FilamentUi::text('Status'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
