<?php

namespace Modules\Campus\Filament\Resources\StudyPrograms;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\StudyPrograms\Pages\CreateStudyProgram;
use Modules\Campus\Filament\Resources\StudyPrograms\Pages\EditStudyProgram;
use Modules\Campus\Filament\Resources\StudyPrograms\Pages\ListStudyPrograms;
use Modules\Campus\Filament\Resources\StudyPrograms\Pages\ViewStudyProgram;
use Modules\Campus\Filament\Resources\StudyPrograms\Schemas\StudyProgramForm;
use Modules\Campus\Filament\Resources\StudyPrograms\Schemas\StudyProgramInfolist;
use Modules\Campus\Filament\Resources\StudyPrograms\Tables\StudyProgramsTable;
use Modules\Campus\Models\StudyProgram;

class StudyProgramResource extends LocalizedResource
{
    protected static ?string $model = StudyProgram::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StudyProgramForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudyProgramInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudyProgramsTable::configure($table);
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
            'index' => ListStudyPrograms::route('/'),
            'create' => CreateStudyProgram::route('/create'),
            'view' => ViewStudyProgram::route('/{record}'),
            'edit' => EditStudyProgram::route('/{record}/edit'),
        ];
    }
}
