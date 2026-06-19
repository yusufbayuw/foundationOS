<?php

namespace Modules\Enrollment\Filament\Resources\LeadSources\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class LeadSourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
