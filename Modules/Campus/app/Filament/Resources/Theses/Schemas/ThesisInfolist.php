<?php

namespace Modules\Campus\Filament\Resources\Theses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ThesisInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('collageStudent.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Collage student')),
                TextEntry::make('advisorLecturer.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Advisor lecturer'))
                    ->placeholder('-'),
                TextEntry::make('examinerLecturer.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Examiner lecturer'))
                    ->placeholder('-'),
                TextEntry::make('title')
                    ->label(\Modules\Core\Support\FilamentUi::field('title')),
                TextEntry::make('research_area')
                    ->label(\Modules\Core\Support\FilamentUi::field('research_area'))
                    ->placeholder('-'),
                TextEntry::make('proposal_submitted_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('proposal_submitted_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('defense_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('defense_date'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('grade_letter')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_letter'))
                    ->placeholder('-'),
                TextEntry::make('grade_point')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_point'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('document_path')
                    ->label(\Modules\Core\Support\FilamentUi::field('document_path'))
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
