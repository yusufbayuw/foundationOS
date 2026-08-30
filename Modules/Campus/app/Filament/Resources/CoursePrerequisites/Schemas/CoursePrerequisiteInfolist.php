<?php

namespace Modules\Campus\Filament\Resources\CoursePrerequisites\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class CoursePrerequisiteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(FilamentUi::text('Tenant')),
                TextEntry::make('course.name')
                    ->label(FilamentUi::text('Course')),
                TextEntry::make('prerequisiteCourse.name')
                    ->label(FilamentUi::text('Prerequisite course')),
                TextEntry::make('min_grade')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_strict')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('is_required')
                    ->boolean(),
                TextEntry::make('note')
                    ->placeholder('-'),
            ]);
    }
}
