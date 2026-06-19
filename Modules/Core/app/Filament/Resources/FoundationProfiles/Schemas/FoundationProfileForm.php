<?php

namespace Modules\Core\Filament\Resources\FoundationProfiles\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class FoundationProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Textarea::make('vision')
                    ->columnSpanFull(),
                Textarea::make('mission')
                    ->columnSpanFull(),
                Textarea::make('core_values')
                    ->columnSpanFull(),
                TextInput::make('logo_path'),
                TextInput::make('profile_document_path'),
            ]);
    }
}
