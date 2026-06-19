<?php

namespace Modules\Enrollment\Filament\Resources\LeadActivities\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class LeadActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('lead_id')
                    ->relationship('lead', 'id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('activity_type')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                DateTimePicker::make('activity_at')
                    ->required(),
            ]);
    }
}
