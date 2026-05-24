<?php

namespace Modules\Campus\Filament\Resources\CourseOfferingLecturers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CourseOfferingLecturerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('courseOffering.id')
                    ->label('Course offering'),
                TextEntry::make('lecturer.id')
                    ->label('Lecturer'),
                TextEntry::make('role')
                    ->badge(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('assigned_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('removed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
