<?php

namespace Modules\Ai\Filament\Resources\AiPromptTemplates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class AiPromptTemplateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('code')
                        ->label(FilamentUi::field('code'))
                        ->placeholder('-'),
                    TextEntry::make('name')
                        ->label(FilamentUi::field('name'))
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->placeholder('-'),
                    TextEntry::make('organization.name')
                        ->label(FilamentUi::field('organization_id'))
                        ->placeholder('-'),
                    TextEntry::make('description')
                        ->label(FilamentUi::field('description'))
                        ->placeholder('-')
                        ->columnSpanFull(),
                    TextEntry::make('meta')
                        ->label(FilamentUi::field('meta'))
                        ->placeholder('-')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
