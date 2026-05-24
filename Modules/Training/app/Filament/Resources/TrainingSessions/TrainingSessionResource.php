<?php

namespace Modules\Training\Filament\Resources\TrainingSessions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Training\Filament\Resources\TrainingSessions\Pages\CreateTrainingSession;
use Modules\Training\Filament\Resources\TrainingSessions\Pages\EditTrainingSession;
use Modules\Training\Filament\Resources\TrainingSessions\Pages\ListTrainingSessions;
use Modules\Training\Filament\Resources\TrainingSessions\Pages\ViewTrainingSession;
use Modules\Training\Filament\Resources\TrainingSessions\Schemas\TrainingSessionForm;
use Modules\Training\Filament\Resources\TrainingSessions\Schemas\TrainingSessionInfolist;
use Modules\Training\Filament\Resources\TrainingSessions\Tables\TrainingSessionsTable;
use Modules\Training\Models\TrainingSession;

class TrainingSessionResource extends ModuleResource
{
    protected static ?string $model = TrainingSession::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TrainingSessionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainingSessionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingSessionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingSessions::route('/'),
            'create' => CreateTrainingSession::route('/create'),
            'view' => ViewTrainingSession::route('/{record}'),
            'edit' => EditTrainingSession::route('/{record}/edit'),
        ];
    }
}
