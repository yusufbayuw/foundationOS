<?php

namespace Modules\Campus\Filament\Resources\StudyResults;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\StudyResults\Pages\CreateStudyResult;
use Modules\Campus\Filament\Resources\StudyResults\Pages\EditStudyResult;
use Modules\Campus\Filament\Resources\StudyResults\Pages\ListStudyResults;
use Modules\Campus\Filament\Resources\StudyResults\Pages\ViewStudyResult;
use Modules\Campus\Filament\Resources\StudyResults\Schemas\StudyResultForm;
use Modules\Campus\Filament\Resources\StudyResults\Schemas\StudyResultInfolist;
use Modules\Campus\Filament\Resources\StudyResults\Tables\StudyResultsTable;
use Modules\Campus\Models\StudyResult;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;

class StudyResultResource extends LocalizedResource
{
    protected static ?string $model = StudyResult::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StudyResultForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudyResultInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudyResultsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudyResults::route('/'),
            'create' => CreateStudyResult::route('/create'),
            'view' => ViewStudyResult::route('/{record}'),
            'edit' => EditStudyResult::route('/{record}/edit'),
        ];
    }
}
