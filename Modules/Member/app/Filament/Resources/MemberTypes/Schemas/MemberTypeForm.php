<?php

namespace Modules\Member\Filament\Resources\MemberTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Modules\Core\Support\FilamentUi;

class MemberTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Member type'))
                    ->description('Defines a reusable membership category for registration and review workflows.')
                    ->schema([
                        TextInput::make('code')
                            ->helperText(FilamentUi::text('Stable lowercase identifier used by integrations and registration forms.'))
                            ->required()
                            ->alphaDash()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(fn (string $state): string => Str::lower($state)),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
