<?php

namespace Modules\Enrollment\Filament\Resources\ExamResults;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Enrollment\Filament\Resources\ExamResults\Pages\CreateExamResult;
use Modules\Enrollment\Filament\Resources\ExamResults\Pages\EditExamResult;
use Modules\Enrollment\Filament\Resources\ExamResults\Pages\ListExamResults;
use Modules\Enrollment\Filament\Resources\ExamResults\Pages\ViewExamResult;
use Modules\Enrollment\Filament\Resources\ExamResults\Schemas\ExamResultForm;
use Modules\Enrollment\Filament\Resources\ExamResults\Schemas\ExamResultInfolist;
use Modules\Enrollment\Filament\Resources\ExamResults\Tables\ExamResultsTable;
use Modules\Enrollment\Models\ExamResult;

class ExamResultResource extends LocalizedResource
{
    protected static ?string $model = ExamResult::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ExamResultForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExamResultInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamResultsTable::configure($table);
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
            'index' => ListExamResults::route('/'),
            'create' => CreateExamResult::route('/create'),
            'view' => ViewExamResult::route('/{record}'),
            'edit' => EditExamResult::route('/{record}/edit'),
        ];
    }
}
