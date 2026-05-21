<?php

namespace Modules\Campus\Filament\Resources\StudyPlans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StudyPlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Relationships')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('collageStudent.id')
                            ->label(FilamentUi::text('Collage student')),
                        TextEntry::make('academicPeriod.name')
                            ->label(FilamentUi::text('Academic period'))
                            ->placeholder('-'),
                    ]),

                Section::make('Plan Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('plan_number')
                            ->label(FilamentUi::field('plan_number'))
                            ->placeholder('-'),
                        TextEntry::make('total_credits')
                            ->label(FilamentUi::field('total_credits'))
                            ->numeric(),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('approved_by')
                            ->label(FilamentUi::field('approved_by'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make('Approval')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('submitted_at')
                            ->label(FilamentUi::field('submitted_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('approved_at')
                            ->label(FilamentUi::field('approved_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
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
