<?php

namespace Modules\Campus\Filament\Resources\StudyPlans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudyPlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('collageStudent.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Collage student')),
                TextEntry::make('academicPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Academic period'))
                    ->placeholder('-'),
                TextEntry::make('approved_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('plan_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('plan_number'))
                    ->placeholder('-'),
                TextEntry::make('total_credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_credits'))
                    ->numeric(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('submitted_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('submitted_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('approved_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_at'))
                    ->dateTime()
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
