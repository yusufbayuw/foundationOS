<?php

namespace Modules\Exam\Filament\Resources\ExamParticipants;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Exam\Filament\Resources\ExamParticipants\Pages\CreateExamParticipant;
use Modules\Exam\Filament\Resources\ExamParticipants\Pages\EditExamParticipant;
use Modules\Exam\Filament\Resources\ExamParticipants\Pages\ListExamParticipants;
use Modules\Exam\Filament\Resources\ExamParticipants\Pages\ViewExamParticipant;
use Modules\Exam\Filament\Resources\ExamParticipants\Schemas\ExamParticipantForm;
use Modules\Exam\Filament\Resources\ExamParticipants\Schemas\ExamParticipantInfolist;
use Modules\Exam\Filament\Resources\ExamParticipants\Tables\ExamParticipantsTable;
use Modules\Exam\Models\ExamParticipant;

class ExamParticipantResource extends LocalizedResource
{
    protected static ?string $model = ExamParticipant::class;

    protected static ?string $recordTitleAttribute = 'student_name';

    public static function form(Schema $schema): Schema
    {
        return ExamParticipantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExamParticipantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamParticipantsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamParticipants::route('/'),
            'create' => CreateExamParticipant::route('/create'),
            'view' => ViewExamParticipant::route('/{record}'),
            'edit' => EditExamParticipant::route('/{record}/edit'),
        ];
    }
}
