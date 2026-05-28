<?php

namespace Modules\Exam\Filament\Resources\ExamQuestionBanks;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Exam\Filament\Resources\ExamQuestionBanks\Pages\CreateExamQuestionBank;
use Modules\Exam\Filament\Resources\ExamQuestionBanks\Pages\EditExamQuestionBank;
use Modules\Exam\Filament\Resources\ExamQuestionBanks\Pages\ListExamQuestionBanks;
use Modules\Exam\Filament\Resources\ExamQuestionBanks\Pages\ViewExamQuestionBank;
use Modules\Exam\Filament\Resources\ExamQuestionBanks\RelationManagers\ExamQuestionsRelationManager;
use Modules\Exam\Filament\Resources\ExamQuestionBanks\Schemas\ExamQuestionBankForm;
use Modules\Exam\Filament\Resources\ExamQuestionBanks\Schemas\ExamQuestionBankInfolist;
use Modules\Exam\Filament\Resources\ExamQuestionBanks\Tables\ExamQuestionBanksTable;
use Modules\Exam\Models\ExamQuestionBank;

class ExamQuestionBankResource extends LocalizedResource
{
    protected static ?string $model = ExamQuestionBank::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ExamQuestionBankForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExamQuestionBankInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamQuestionBanksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ExamQuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamQuestionBanks::route('/'),
            'create' => CreateExamQuestionBank::route('/create'),
            'view' => ViewExamQuestionBank::route('/{record}'),
            'edit' => EditExamQuestionBank::route('/{record}/edit'),
        ];
    }
}
