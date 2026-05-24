<?php

namespace Modules\School\Filament\Resources\StudentAbsences;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\School\Filament\Resources\StudentAbsences\Pages\CreateStudentAbsence;
use Modules\School\Filament\Resources\StudentAbsences\Pages\EditStudentAbsence;
use Modules\School\Filament\Resources\StudentAbsences\Pages\ListStudentAbsences;
use Modules\School\Filament\Resources\StudentAbsences\Pages\ViewStudentAbsence;
use Modules\School\Filament\Resources\StudentAbsences\Schemas\StudentAbsenceForm;
use Modules\School\Filament\Resources\StudentAbsences\Schemas\StudentAbsenceInfolist;
use Modules\School\Filament\Resources\StudentAbsences\Tables\StudentAbsencesTable;
use Modules\School\Models\StudentAbsence;

class StudentAbsenceResource extends ModuleResource
{
    protected static ?string $model = StudentAbsence::class;

    protected static ?string $recordTitleAttribute = 'status';

    public static function form(Schema $schema): Schema
    {
        return StudentAbsenceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentAbsenceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentAbsencesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentAbsences::route('/'),
            'create' => CreateStudentAbsence::route('/create'),
            'view' => ViewStudentAbsence::route('/{record}'),
            'edit' => EditStudentAbsence::route('/{record}/edit'),
        ];
    }
}
