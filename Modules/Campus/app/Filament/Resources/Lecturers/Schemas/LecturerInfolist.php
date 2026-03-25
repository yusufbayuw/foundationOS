<?php

namespace Modules\Campus\Filament\Resources\Lecturers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LecturerInfolist
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
                TextEntry::make('user.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('User'))
                    ->placeholder('-'),
                TextEntry::make('studyProgram.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Study program'))
                    ->placeholder('-'),
                TextEntry::make('nidn')
                    ->label(\Modules\Core\Support\FilamentUi::field('nidn'))
                    ->placeholder('-'),
                TextEntry::make('employee_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('employee_number'))
                    ->placeholder('-'),
                TextEntry::make('full_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('full_name'))
                    ->placeholder('-'),
                TextEntry::make('academic_title_prefix')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_title_prefix'))
                    ->placeholder('-'),
                TextEntry::make('academic_title_suffix')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_title_suffix'))
                    ->placeholder('-'),
                TextEntry::make('functional_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('functional_position'))
                    ->placeholder('-'),
                TextEntry::make('employment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('employment_status'))
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->placeholder('-'),
                TextEntry::make('join_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('join_date'))
                    ->date()
                    ->placeholder('-'),
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
