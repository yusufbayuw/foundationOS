<?php

namespace Modules\Workflow\Filament\Resources\WorkflowDelegations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class WorkflowDelegationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('from_user_id')
                        ->label(FilamentUi::field('from_user_id'))
                        ->numeric(),
                    TextInput::make('to_user_id')
                        ->label(FilamentUi::field('to_user_id'))
                        ->numeric(),
                    TextInput::make('valid_from')
                        ->label(FilamentUi::field('valid_from')),
                    TextInput::make('valid_until')
                        ->label(FilamentUi::field('valid_until')),
                    TextInput::make('workflow_type_codes')
                        ->label(FilamentUi::field('workflow_type_codes')),
                    TextInput::make('is_active')
                        ->label(FilamentUi::field('is_active')),
                ])
                ->columns(2),
        ]);
    }
}
