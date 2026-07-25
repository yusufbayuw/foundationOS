<?php

namespace Modules\Cms\Filament\Resources\HomepageSections\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Cms\Models\HomepageSection;

class HomepageSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('type')
                    ->options(array_combine(HomepageSection::standardTypes(), HomepageSection::standardTypes()))
                    ->required(),
                TextInput::make('title')
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->default(true),
                KeyValue::make('settings')
                    ->columnSpanFull(),
            ]);
    }
}
