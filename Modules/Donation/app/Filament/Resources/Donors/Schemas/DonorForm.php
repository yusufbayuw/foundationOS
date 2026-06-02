<?php

namespace Modules\Donation\Filament\Resources\Donors\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class DonorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General information'))
                    ->schema([
                        TenantField::make(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('email')
                            ->label(FilamentUi::field('email'))
                            ->email(),
                        TextInput::make('phone')
                            ->label(FilamentUi::field('phone'))
                            ->tel(),
                        Toggle::make('is_anonymous')
                            ->label(FilamentUi::field('is_anonymous'))
                            ->default(false)
                            ->live(),
                        TagsInput::make('tags')
                            ->label(FilamentUi::field('tags'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
