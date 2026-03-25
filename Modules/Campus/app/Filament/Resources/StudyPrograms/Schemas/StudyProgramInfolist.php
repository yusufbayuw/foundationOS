<?php

namespace Modules\Campus\Filament\Resources\StudyPrograms\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudyProgramInfolist
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
                TextEntry::make('faculty.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Faculty'))
                    ->placeholder('-'),
                TextEntry::make('headOfProgram.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Head of program'))
                    ->placeholder('-'),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->placeholder('-'),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('degree_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('degree_level'))
                    ->placeholder('-'),
                TextEntry::make('accreditation')
                    ->label(\Modules\Core\Support\FilamentUi::field('accreditation'))
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('total_credits_required')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_credits_required'))
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
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
