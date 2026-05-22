<?php

namespace Modules\School\Filament\Resources\Assessments\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class AssessmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('academicPeriod.name')
                            ->label(FilamentUi::text('Academic period'))
                            ->placeholder('-'),
                        TextEntry::make('subject.name')
                            ->label(FilamentUi::text('Subject')),
                        TextEntry::make('class_id')
                            ->label(FilamentUi::field('class_id'))
                            ->numeric(),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Classification & Scoring'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('type')
                            ->label(FilamentUi::field('type'))
                            ->placeholder('-'),
                        TextEntry::make('assessment_category')
                            ->label(FilamentUi::field('assessment_category'))
                            ->placeholder('-'),
                        TextEntry::make('weight')
                            ->label(FilamentUi::field('weight'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('max_score')
                            ->label(FilamentUi::field('max_score'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('passing_score')
                            ->label(FilamentUi::field('passing_score'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Schedule'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('schedule_date')
                            ->label(FilamentUi::field('schedule_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('start_time')
                            ->label(FilamentUi::field('start_time'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('end_time')
                            ->label(FilamentUi::field('end_time'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('duration_minutes')
                            ->label(FilamentUi::field('duration_minutes'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Instructions & Attachments'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('instructions')
                            ->label(FilamentUi::field('instructions'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('attachments')
                            ->label(FilamentUi::field('attachments'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Publication & Attempts'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_published')
                            ->boolean(),
                        TextEntry::make('published_at')
                            ->label(FilamentUi::field('published_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        IconEntry::make('allow_retake')
                            ->boolean(),
                        TextEntry::make('max_attempts')
                            ->label(FilamentUi::field('max_attempts'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
