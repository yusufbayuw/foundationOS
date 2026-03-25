<?php

namespace Modules\School\Filament\Resources\Assessments\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AssessmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization'))
                    ->placeholder('-'),
                TextEntry::make('academicPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Academic period'))
                    ->placeholder('-'),
                TextEntry::make('subject.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Subject')),
                TextEntry::make('class_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_id'))
                    ->numeric(),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->placeholder('-'),
                TextEntry::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->placeholder('-'),
                TextEntry::make('assessment_category')
                    ->label(\Modules\Core\Support\FilamentUi::field('assessment_category'))
                    ->placeholder('-'),
                TextEntry::make('weight')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('max_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_score'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('passing_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('passing_score'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('schedule_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('schedule_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('duration_minutes')
                    ->label(\Modules\Core\Support\FilamentUi::field('duration_minutes'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('instructions')
                    ->label(\Modules\Core\Support\FilamentUi::field('instructions'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('attachments')
                    ->label(\Modules\Core\Support\FilamentUi::field('attachments'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_published')
                    ->boolean(),
                TextEntry::make('published_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('published_at'))
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('allow_retake')
                    ->boolean(),
                TextEntry::make('max_attempts')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_attempts'))
                    ->numeric()
                    ->placeholder('-'),
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
