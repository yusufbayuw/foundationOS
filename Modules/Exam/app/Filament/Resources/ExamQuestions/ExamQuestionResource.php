<?php

namespace Modules\Exam\Filament\Resources\ExamQuestions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Exam\Filament\Resources\ExamQuestions\Pages\CreateExamQuestion;
use Modules\Exam\Filament\Resources\ExamQuestions\Pages\EditExamQuestion;
use Modules\Exam\Filament\Resources\ExamQuestions\Pages\ListExamQuestions;
use Modules\Exam\Filament\Resources\ExamQuestions\Pages\ViewExamQuestion;
use Modules\Exam\Filament\Resources\ExamQuestions\Schemas\ExamQuestionForm;
use Modules\Exam\Filament\Resources\ExamQuestions\Schemas\ExamQuestionInfolist;
use Modules\Exam\Filament\Resources\ExamQuestions\Tables\ExamQuestionsTable;
use Modules\Exam\Models\ExamQuestion;

class ExamQuestionResource extends LocalizedResource
{
    protected static ?string $model = ExamQuestion::class;

    protected static ?string $recordTitleAttribute = 'topic';

    public static function form(Schema $schema): Schema
    {
        return ExamQuestionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExamQuestionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamQuestionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamQuestions::route('/'),
            'create' => CreateExamQuestion::route('/create'),
            'view' => ViewExamQuestion::route('/{record}'),
            'edit' => EditExamQuestion::route('/{record}/edit'),
        ];
    }
}
