<?php

namespace Modules\Workflow\Filament\Resources\WorkflowDelegations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WorkflowDelegationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('from_user_id')
                        ->label(FilamentUi::field('from_user_id'))
                        ->placeholder('-'),
                    TextEntry::make('to_user_id')
                        ->label(FilamentUi::field('to_user_id'))
                        ->placeholder('-'),
                    TextEntry::make('valid_from')
                        ->label(FilamentUi::field('valid_from'))
                        ->placeholder('-'),
                    TextEntry::make('valid_until')
                        ->label(FilamentUi::field('valid_until'))
                        ->placeholder('-'),
                    TextEntry::make('workflow_type_codes')
                        ->label(FilamentUi::field('workflow_type_codes'))
                        ->placeholder('-'),
                    TextEntry::make('is_active')
                        ->label(FilamentUi::field('is_active'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
