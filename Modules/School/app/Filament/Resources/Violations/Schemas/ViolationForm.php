<?php

namespace Modules\School\Filament\Resources\Violations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ViolationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Violation Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('student_id')
                            ->label(FilamentUi::field('student_id'))
                            ->relationship('student', 'id')
                            ->required(),
                        Select::make('violation_type_id')
                            ->label(FilamentUi::field('violation_type_id'))
                            ->relationship('violationType', 'name'),
                        DatePicker::make('date')
                            ->label(FilamentUi::field('date')),
                        TextInput::make('severity')
                            ->label(FilamentUi::field('severity')),
                        TextInput::make('location')
                            ->label(FilamentUi::field('location')),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                        Textarea::make('witnesses')
                            ->label(FilamentUi::field('witnesses'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Reporting'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('reported_by')
                            ->label(FilamentUi::field('reported_by'))
                            ->numeric(),
                        TextInput::make('handled_by')
                            ->label(FilamentUi::field('handled_by'))
                            ->numeric(),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status')),
                    ]),

                Section::make(FilamentUi::text('Sanctions & Resolution'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('sanctions')
                            ->label(FilamentUi::field('sanctions'))
                            ->columnSpanFull(),
                        TextInput::make('sanction_duration_days')
                            ->label(FilamentUi::field('sanction_duration_days'))
                            ->numeric(),
                        Toggle::make('parent_notified')
                            ->label(FilamentUi::field('parent_notified'))
                            ->required(),
                        DatePicker::make('parent_meeting_date')
                            ->label(FilamentUi::field('parent_meeting_date')),
                        Textarea::make('resolution_notes')
                            ->label(FilamentUi::field('resolution_notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
