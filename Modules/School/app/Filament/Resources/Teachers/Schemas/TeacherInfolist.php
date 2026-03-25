<?php

namespace Modules\School\Filament\Resources\Teachers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TeacherInfolist
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
                TextEntry::make('nip')
                    ->label(\Modules\Core\Support\FilamentUi::field('nip'))
                    ->placeholder('-'),
                TextEntry::make('nuptk')
                    ->label(\Modules\Core\Support\FilamentUi::field('nuptk'))
                    ->placeholder('-'),
                TextEntry::make('nrg')
                    ->label(\Modules\Core\Support\FilamentUi::field('nrg'))
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->placeholder('-'),
                TextEntry::make('employment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('employment_status'))
                    ->placeholder('-'),
                TextEntry::make('join_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('join_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('resignation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('resignation_date'))
                    ->date()
                    ->placeholder('-'),
                IconEntry::make('is_certified')
                    ->boolean(),
                TextEntry::make('certification_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('certification_year'))
                    ->placeholder('-'),
                TextEntry::make('certification_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('certification_number'))
                    ->placeholder('-'),
                TextEntry::make('highest_education')
                    ->label(\Modules\Core\Support\FilamentUi::field('highest_education'))
                    ->placeholder('-'),
                TextEntry::make('major_study')
                    ->label(\Modules\Core\Support\FilamentUi::field('major_study'))
                    ->placeholder('-'),
                TextEntry::make('university')
                    ->label(\Modules\Core\Support\FilamentUi::field('university'))
                    ->placeholder('-'),
                TextEntry::make('functional_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('functional_position'))
                    ->placeholder('-'),
                TextEntry::make('structural_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('structural_position'))
                    ->placeholder('-'),
                TextEntry::make('subject_specializations')
                    ->label(\Modules\Core\Support\FilamentUi::field('subject_specializations'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('class_advisor_history')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_advisor_history'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('teaching_hours_per_week')
                    ->label(\Modules\Core\Support\FilamentUi::field('teaching_hours_per_week'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('base_salary')
                    ->label(\Modules\Core\Support\FilamentUi::field('base_salary'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('allowance')
                    ->label(\Modules\Core\Support\FilamentUi::field('allowance'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('bpjs_tk_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_tk_number'))
                    ->placeholder('-'),
                TextEntry::make('bpjs_kes_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_kes_number'))
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
