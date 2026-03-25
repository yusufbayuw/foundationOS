<?php

namespace Modules\School\Filament\Resources\Subjects\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SubjectInfolist
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
                TextEntry::make('curriculum.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Curriculum'))
                    ->placeholder('-'),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('short_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('short_name'))
                    ->placeholder('-'),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('grade_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_level'))
                    ->placeholder('-'),
                TextEntry::make('credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('credits'))
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_mandatory')
                    ->boolean(),
                TextEntry::make('subject_group')
                    ->label(\Modules\Core\Support\FilamentUi::field('subject_group'))
                    ->placeholder('-'),
                IconEntry::make('has_practicum')
                    ->boolean(),
                TextEntry::make('color_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('color_code'))
                    ->placeholder('-'),
                TextEntry::make('icon')
                    ->label(\Modules\Core\Support\FilamentUi::field('icon'))
                    ->placeholder('-'),
                TextEntry::make('learning_outcomes')
                    ->label(\Modules\Core\Support\FilamentUi::field('learning_outcomes'))
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
