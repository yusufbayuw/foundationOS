<?php

namespace Modules\Risk\Filament\Resources\RiskAssessments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class RiskAssessmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('organization_id')
                        ->label(FilamentUi::field('organization_id'))
                        ->placeholder('-'),
                    TextEntry::make('code')
                        ->label(FilamentUi::field('code'))
                        ->placeholder('-'),
                    TextEntry::make('name')
                        ->label(FilamentUi::field('name'))
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->placeholder('-'),
                    TextEntry::make('description')
                        ->label(FilamentUi::field('description'))
                        ->placeholder('-'),
                    TextEntry::make('meta')
                        ->label(FilamentUi::field('meta'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
