<?php

namespace Modules\School\Filament\Resources\SchoolClasses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class SchoolClassInfolist
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
                            ->label(FilamentUi::text('Academic period')),
                        TextEntry::make('department.name')
                            ->label(FilamentUi::text('Department'))
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                    ]),

                Section::make(FilamentUi::text('Teachers'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('homeroomTeacher.id')
                            ->label(FilamentUi::text('Homeroom teacher'))
                            ->placeholder('-'),
                        TextEntry::make('assistantTeacher.id')
                            ->label(FilamentUi::text('Assistant teacher'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Capacity & Status'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('grade_level')
                            ->label(FilamentUi::field('grade_level'))
                            ->placeholder('-'),
                        TextEntry::make('capacity')
                            ->label(FilamentUi::field('capacity'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('student_count')
                            ->label(FilamentUi::field('student_count'))
                            ->numeric(),
                        IconEntry::make('is_active')
                            ->boolean(),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
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
