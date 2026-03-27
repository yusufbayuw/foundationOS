<?php

namespace Modules\School\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name'),
                Select::make('user_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('user_id'))
                    ->relationship('user', 'name'),
                TextInput::make('nip')
                    ->label(\Modules\Core\Support\FilamentUi::field('nip')),
                TextInput::make('nuptk')
                    ->label(\Modules\Core\Support\FilamentUi::field('nuptk')),
                TextInput::make('nrg')
                    ->label(\Modules\Core\Support\FilamentUi::field('nrg')),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextInput::make('employment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('employment_status')),
                DatePicker::make('join_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('join_date')),
                DatePicker::make('resignation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('resignation_date')),
                Toggle::make('is_certified')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_certified'))
                    ->required(),
                TextInput::make('certification_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('certification_year')),
                TextInput::make('certification_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('certification_number')),
                TextInput::make('highest_education')
                    ->label(\Modules\Core\Support\FilamentUi::field('highest_education')),
                TextInput::make('major_study')
                    ->label(\Modules\Core\Support\FilamentUi::field('major_study')),
                TextInput::make('university')
                    ->label(\Modules\Core\Support\FilamentUi::field('university')),
                TextInput::make('functional_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('functional_position')),
                TextInput::make('structural_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('structural_position')),
                Textarea::make('subject_specializations')
                    ->label(\Modules\Core\Support\FilamentUi::field('subject_specializations'))
                    ->columnSpanFull(),
                Textarea::make('class_advisor_history')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_advisor_history'))
                    ->columnSpanFull(),
                TextInput::make('teaching_hours_per_week')
                    ->label(\Modules\Core\Support\FilamentUi::field('teaching_hours_per_week'))
                    ->numeric(),
                TextInput::make('base_salary')
                    ->label(\Modules\Core\Support\FilamentUi::field('base_salary'))
                    ->numeric(),
                TextInput::make('allowance')
                    ->label(\Modules\Core\Support\FilamentUi::field('allowance'))
                    ->numeric(),
                TextInput::make('bpjs_tk_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_tk_number')),
                TextInput::make('bpjs_kes_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_kes_number'))
            ]);
    }
}
