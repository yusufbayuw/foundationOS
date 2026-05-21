<?php

namespace Modules\Campus\Filament\Resources\Faculties\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class FacultyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Faculty Details')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        TextInput::make('code')
                            ->label(\Modules\Core\Support\FilamentUi::field('code')),
                        TextInput::make('name')
                            ->label(\Modules\Core\Support\FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('short_name')
                            ->label(\Modules\Core\Support\FilamentUi::field('short_name')),
                        Textarea::make('description')
                            ->label(\Modules\Core\Support\FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Contact Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('office_phone')
                            ->label(\Modules\Core\Support\FilamentUi::field('office_phone'))
                            ->tel(),
                        TextInput::make('office_email')
                            ->label(\Modules\Core\Support\FilamentUi::field('office_email'))
                            ->email(),
                    ]),

                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
