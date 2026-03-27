<?php

namespace Modules\Campus\Filament\Resources\StudyPrograms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class StudyProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name'),
                Select::make('faculty_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('faculty_id'))
                    ->relationship('faculty', 'name'),
                Select::make('head_of_program_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('head_of_program_id'))
                    ->relationship('headOfProgram', 'id'),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('degree_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('degree_level')),
                TextInput::make('accreditation')
                    ->label(\Modules\Core\Support\FilamentUi::field('accreditation')),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                TextInput::make('total_credits_required')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_credits_required'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
            ]);
    }
}
