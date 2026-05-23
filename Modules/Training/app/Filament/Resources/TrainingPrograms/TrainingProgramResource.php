<?php

namespace Modules\Training\Filament\Resources\TrainingPrograms;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Training\Filament\Resources\TrainingPrograms\Pages\CreateTrainingProgram;
use Modules\Training\Filament\Resources\TrainingPrograms\Pages\EditTrainingProgram;
use Modules\Training\Filament\Resources\TrainingPrograms\Pages\ListTrainingPrograms;
use Modules\Training\Filament\Resources\TrainingPrograms\Pages\ViewTrainingProgram;
use Modules\Training\Filament\Resources\TrainingPrograms\Schemas\TrainingProgramForm;
use Modules\Training\Filament\Resources\TrainingPrograms\Tables\TrainingProgramsTable;
use Modules\Training\Models\TrainingProgram;

class TrainingProgramResource extends ModuleResource
{
    protected static ?string $model = TrainingProgram::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TrainingProgramForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingProgramsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingPrograms::route('/'),
            'create' => CreateTrainingProgram::route('/create'),
            'view' => ViewTrainingProgram::route('/{record}'),
            'edit' => EditTrainingProgram::route('/{record}/edit'),
        ];
    }
}
