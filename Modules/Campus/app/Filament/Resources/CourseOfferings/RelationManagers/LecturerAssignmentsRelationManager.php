<?php

namespace Modules\Campus\Filament\Resources\CourseOfferings\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Campus\Enums\CourseOfferingLecturerRole;
use Modules\Campus\Models\Lecturer;
use Modules\Core\Support\FilamentUi;

class LecturerAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'lecturerAssignments';

    protected static ?string $title = 'Lecturers';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lecturer_id')
                    ->label(FilamentUi::text('Lecturer'))
                    ->options(fn () => Lecturer::query()
                        ->where('tenant_id', $this->getOwnerRecord()->tenant_id)
                        ->where('is_active', true)
                        ->pluck('full_name', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('role')
                    ->options([
                        CourseOfferingLecturerRole::Primary->value => 'Primary (editing teacher)',
                        CourseOfferingLecturerRole::Assistant->value => 'Assistant (non-editing)',
                    ])
                    ->default(CourseOfferingLecturerRole::Primary->value)
                    ->required(),
                Toggle::make('is_active')->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('lecturer_id')
            ->columns([
                TextColumn::make('lecturer.full_name')->label(FilamentUi::text('Lecturer'))->searchable(),
                TextColumn::make('role')->badge(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('assigned_at')->dateTime()->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['tenant_id'] = $this->getOwnerRecord()->tenant_id;
                        $data['assigned_at'] = now();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
