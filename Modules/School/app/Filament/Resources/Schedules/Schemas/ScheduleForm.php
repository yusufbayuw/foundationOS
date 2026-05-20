<?php

namespace Modules\School\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Schedule Details')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('academic_period_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('academic_period_id'))
                            ->relationship('academicPeriod', 'name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (\Filament\Forms\Get $get, ?string $state, $livewire) => self::checkConflicts($get, $state, $livewire)),
                        TextInput::make('class_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('class_id'))
                            ->required()
                            ->numeric()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (\Filament\Forms\Get $get, ?string $state, $livewire) => self::checkConflicts($get, $state, $livewire)),
                        Select::make('subject_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('subject_id'))
                            ->relationship('subject', 'name')
                            ->required(),
                        Select::make('teacher_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('teacher_id'))
                            ->relationship('teacher', 'id')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (\Filament\Forms\Get $get, ?string $state, $livewire) => self::checkConflicts($get, $state, $livewire)),
                        TextInput::make('schedule_type')
                            ->label(\Modules\Core\Support\FilamentUi::field('schedule_type')),
                        TextInput::make('semester')
                            ->label(\Modules\Core\Support\FilamentUi::field('semester')),
                    ]),

                Section::make('Time & Location')
                    ->columns(2)
                    ->schema([
                        TextInput::make('day_of_week')
                            ->label(\Modules\Core\Support\FilamentUi::field('day_of_week'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (\Filament\Forms\Get $get, ?string $state, $livewire) => self::checkConflicts($get, $state, $livewire)),
                        TextInput::make('duration_minutes')
                            ->label(\Modules\Core\Support\FilamentUi::field('duration_minutes'))
                            ->numeric(),
                        TimePicker::make('start_time')
                            ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (\Filament\Forms\Get $get, ?string $state, $livewire) => self::checkConflicts($get, $state, $livewire)),
                        TimePicker::make('end_time')
                            ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (\Filament\Forms\Get $get, ?string $state, $livewire) => self::checkConflicts($get, $state, $livewire)),
                    ]),

                Section::make('Recurrence & Notes')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_recurring')
                            ->label(\Modules\Core\Support\FilamentUi::field('is_recurring'))
                            ->required(),
                        DatePicker::make('effective_date')
                            ->label(\Modules\Core\Support\FilamentUi::field('effective_date')),
                        DatePicker::make('expiry_date')
                            ->label(\Modules\Core\Support\FilamentUi::field('expiry_date')),
                        Textarea::make('notes')
                            ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function checkConflicts(\Filament\Forms\Get $get, ?string $state, $livewire): void
    {
        if (empty($state)) {
            return;
        }

        $data = [
            'academic_period_id' => $get('academic_period_id'),
            'class_id' => $get('class_id'),
            'teacher_id' => $get('teacher_id'),
            'day_of_week' => $get('day_of_week'),
            'start_time' => $get('start_time'),
            'end_time' => $get('end_time'),
        ];

        // Ensure minimum fields are present before checking
        if (
            empty($data['academic_period_id']) ||
            empty($data['day_of_week']) ||
            empty($data['start_time']) ||
            empty($data['end_time'])
        ) {
            return;
        }

        $excludeId = null;
        if (method_exists($livewire, 'getRecord')) {
            $record = $livewire->getRecord();
            $excludeId = $record ? $record->id : null;
        }

        $checker = new \Modules\School\Services\ScheduleConflictChecker();
        $conflicts = $checker->checkConflicts($data, $excludeId);

        if (! empty($conflicts)) {
            \Filament\Notifications\Notification::make()
                ->warning()
                ->title('Konflik Jadwal Terdeteksi')
                ->body(implode('<br>', $conflicts))
                ->send();
        }
    }
}
