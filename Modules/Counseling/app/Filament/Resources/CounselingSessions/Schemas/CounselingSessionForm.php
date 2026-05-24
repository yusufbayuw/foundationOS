<?php

namespace Modules\Counseling\Filament\Resources\CounselingSessions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CounselingSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->relationship('organization', 'name'),
                TextInput::make('code'),
                TextInput::make('name'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('meta')
                    ->columnSpanFull(),
                TextInput::make('counseling_case_id')
                    ->numeric(),
                DateTimePicker::make('scheduled_at'),
            ]);
    }
}
