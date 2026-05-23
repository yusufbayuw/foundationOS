<?php

namespace Modules\Boarding\Filament\Resources\Dormitories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class DormitoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))->schema([
                TextInput::make('code')->label(FilamentUi::field('code')),
                TextInput::make('name')->label(FilamentUi::field('name'))->required(),
                Select::make('status')->label(FilamentUi::field('status'))->options([
                    'active' => FilamentUi::text('Active'),
                    'inactive' => FilamentUi::text('Inactive'),
                ])->default('active'),
                Textarea::make('description')->label(FilamentUi::field('description'))->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
