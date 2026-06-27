<?php

namespace Modules\School\Filament\Resources\Schedules\Schemas;

use App\Support\TypedValue;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\School\Services\ScheduleConflictChecker;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Schedule Details'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('academic_period_id')
                            ->label(FilamentUi::field('academic_period_id'))
                            ->relationship('academicPeriod', 'name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, ?string $state, CreateRecord|EditRecord $livewire) => self::checkConflicts($get, $state, $livewire)),
                        TextInput::make('class_id')
                            ->label(FilamentUi::field('class_id'))
                            ->required()
                            ->numeric()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, ?string $state, CreateRecord|EditRecord $livewire) => self::checkConflicts($get, $state, $livewire)),
                        Select::make('subject_id')
                            ->label(FilamentUi::field('subject_id'))
                            ->relationship('subject', 'name')
                            ->required(),
                        Select::make('teacher_id')
                            ->label(FilamentUi::field('teacher_id'))
                            ->relationship('teacher', 'id')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, ?string $state, CreateRecord|EditRecord $livewire) => self::checkConflicts($get, $state, $livewire)),
                        TextInput::make('schedule_type')
                            ->label(FilamentUi::field('schedule_type')),
                        TextInput::make('semester')
                            ->label(FilamentUi::field('semester')),
                    ]),

                Section::make(FilamentUi::text('Time & Location'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('day_of_week')
                            ->label(FilamentUi::field('day_of_week'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, ?string $state, CreateRecord|EditRecord $livewire) => self::checkConflicts($get, $state, $livewire)),
                        TextInput::make('duration_minutes')
                            ->label(FilamentUi::field('duration_minutes'))
                            ->numeric(),
                        TimePicker::make('start_time')
                            ->label(FilamentUi::field('start_time'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, ?string $state, CreateRecord|EditRecord $livewire) => self::checkConflicts($get, $state, $livewire)),
                        TimePicker::make('end_time')
                            ->label(FilamentUi::field('end_time'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, ?string $state, CreateRecord|EditRecord $livewire) => self::checkConflicts($get, $state, $livewire)),
                    ]),

                Section::make(FilamentUi::text('Recurrence & Notes'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_recurring')
                            ->label(FilamentUi::field('is_recurring'))
                            ->required(),
                        DatePicker::make('effective_date')
                            ->label(FilamentUi::field('effective_date')),
                        DatePicker::make('expiry_date')
                            ->label(FilamentUi::field('expiry_date')),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function checkConflicts(Get $get, ?string $state, CreateRecord|EditRecord $livewire): void
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

        $excludeId = $livewire instanceof EditRecord
            ? TypedValue::nullableInt($livewire->getRecord()->getKey())
            : null;

        $checker = new ScheduleConflictChecker;
        $conflicts = $checker->checkConflicts($data, $excludeId);

        if (! empty($conflicts)) {
            Notification::make()
                ->warning()
                ->title('Konflik Jadwal Terdeteksi')
                ->body(implode('<br>', $conflicts))
                ->send();
        }
    }
}
