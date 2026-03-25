<?php

namespace Modules\Enrollment\Filament\Resources\ExamSchedules;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Enrollment\Filament\Resources\ExamSchedules\Pages\CreateExamSchedule;
use Modules\Enrollment\Filament\Resources\ExamSchedules\Pages\EditExamSchedule;
use Modules\Enrollment\Filament\Resources\ExamSchedules\Pages\ListExamSchedules;
use Modules\Enrollment\Filament\Resources\ExamSchedules\Pages\ViewExamSchedule;
use Modules\Enrollment\Filament\Resources\ExamSchedules\Schemas\ExamScheduleForm;
use Modules\Enrollment\Filament\Resources\ExamSchedules\Schemas\ExamScheduleInfolist;
use Modules\Enrollment\Filament\Resources\ExamSchedules\Tables\ExamSchedulesTable;
use Modules\Enrollment\Models\ExamSchedule;

class ExamScheduleResource extends LocalizedResource
{
    protected static ?string $model = ExamSchedule::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ExamScheduleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExamScheduleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamSchedulesTable::configure($table);
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
            'index' => ListExamSchedules::route('/'),
            'create' => CreateExamSchedule::route('/create'),
            'view' => ViewExamSchedule::route('/{record}'),
            'edit' => EditExamSchedule::route('/{record}/edit'),
        ];
    }
}
