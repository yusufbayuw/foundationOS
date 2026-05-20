<?php

namespace Modules\School\Filament\Resources\Violations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class ViolationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Violation Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('student_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('student_id'))
                            ->relationship('student', 'id')
                            ->required(),
                        Select::make('violation_type_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('violation_type_id'))
                            ->relationship('violationType', 'name'),
                        DatePicker::make('date')
                            ->label(\Modules\Core\Support\FilamentUi::field('date')),
                        TextInput::make('severity')
                            ->label(\Modules\Core\Support\FilamentUi::field('severity')),
                        TextInput::make('location')
                            ->label(\Modules\Core\Support\FilamentUi::field('location')),
                        Textarea::make('description')
                            ->label(\Modules\Core\Support\FilamentUi::field('description'))
                            ->columnSpanFull(),
                        Textarea::make('witnesses')
                            ->label(\Modules\Core\Support\FilamentUi::field('witnesses'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Reporting')
                    ->columns(2)
                    ->schema([
                        TextInput::make('reported_by')
                            ->label(\Modules\Core\Support\FilamentUi::field('reported_by'))
                            ->numeric(),
                        TextInput::make('handled_by')
                            ->label(\Modules\Core\Support\FilamentUi::field('handled_by'))
                            ->numeric(),
                        TextInput::make('status')
                            ->label(\Modules\Core\Support\FilamentUi::field('status')),
                    ]),

                Section::make('Sanctions & Resolution')
                    ->columns(2)
                    ->schema([
                        Textarea::make('sanctions')
                            ->label(\Modules\Core\Support\FilamentUi::field('sanctions'))
                            ->columnSpanFull(),
                        TextInput::make('sanction_duration_days')
                            ->label(\Modules\Core\Support\FilamentUi::field('sanction_duration_days'))
                            ->numeric(),
                        Toggle::make('parent_notified')
                            ->label(\Modules\Core\Support\FilamentUi::field('parent_notified'))
                            ->required(),
                        DatePicker::make('parent_meeting_date')
                            ->label(\Modules\Core\Support\FilamentUi::field('parent_meeting_date')),
                        Textarea::make('resolution_notes')
                            ->label(\Modules\Core\Support\FilamentUi::field('resolution_notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
