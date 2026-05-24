<?php

namespace Modules\Training\Filament\Resources\TrainingBatches;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Training\Filament\Resources\TrainingBatches\Pages\CreateTrainingBatch;
use Modules\Training\Filament\Resources\TrainingBatches\Pages\EditTrainingBatch;
use Modules\Training\Filament\Resources\TrainingBatches\Pages\ListTrainingBatches;
use Modules\Training\Filament\Resources\TrainingBatches\Pages\ViewTrainingBatch;
use Modules\Training\Filament\Resources\TrainingBatches\Schemas\TrainingBatchForm;
use Modules\Training\Filament\Resources\TrainingBatches\Schemas\TrainingBatchInfolist;
use Modules\Training\Filament\Resources\TrainingBatches\Tables\TrainingBatchesTable;
use Modules\Training\Models\TrainingBatch;

class TrainingBatchResource extends ModuleResource
{
    protected static ?string $model = TrainingBatch::class;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return TrainingBatchForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainingBatchInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingBatchesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingBatches::route('/'),
            'create' => CreateTrainingBatch::route('/create'),
            'view' => ViewTrainingBatch::route('/{record}'),
            'edit' => EditTrainingBatch::route('/{record}/edit'),
        ];
    }
}
