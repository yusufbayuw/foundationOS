<?php

namespace Modules\School\Filament\Resources\ClassStudents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClassStudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('academicPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Academic period'))
                    ->placeholder('-'),
                TextEntry::make('class_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_id'))
                    ->numeric(),
                TextEntry::make('student.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Student')),
                TextEntry::make('entry_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('exit_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('exit_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->placeholder('-'),
                TextEntry::make('entry_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_type'))
                    ->placeholder('-'),
                TextEntry::make('exit_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('exit_reason'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('ranking')
                    ->label(\Modules\Core\Support\FilamentUi::field('ranking'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('certificate_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('certificate_number'))
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
