<?php

namespace Modules\School\Filament\Resources\SchoolClasses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SchoolClassInfolist
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
                    ->label(\Modules\Core\Support\FilamentUi::text('Academic period')),
                TextEntry::make('department.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Department'))
                    ->placeholder('-'),
                TextEntry::make('homeroomTeacher.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Homeroom teacher'))
                    ->placeholder('-'),
                TextEntry::make('assistantTeacher.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Assistant teacher'))
                    ->placeholder('-'),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextEntry::make('grade_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_level'))
                    ->placeholder('-'),
                TextEntry::make('capacity')
                    ->label(\Modules\Core\Support\FilamentUi::field('capacity'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('student_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('student_count'))
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
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
