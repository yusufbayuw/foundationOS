<?php

namespace Modules\Exam\Filament\Resources\ExamTokens;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Exam\Filament\Resources\ExamTokens\Pages\CreateExamToken;
use Modules\Exam\Filament\Resources\ExamTokens\Pages\EditExamToken;
use Modules\Exam\Filament\Resources\ExamTokens\Pages\ListExamTokens;
use Modules\Exam\Filament\Resources\ExamTokens\Pages\ViewExamToken;
use Modules\Exam\Filament\Resources\ExamTokens\Schemas\ExamTokenForm;
use Modules\Exam\Filament\Resources\ExamTokens\Schemas\ExamTokenInfolist;
use Modules\Exam\Filament\Resources\ExamTokens\Tables\ExamTokensTable;
use Modules\Exam\Models\ExamToken;

class ExamTokenResource extends LocalizedResource
{
    protected static ?string $model = ExamToken::class;

    protected static ?string $recordTitleAttribute = 'token';

    public static function form(Schema $schema): Schema
    {
        return ExamTokenForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExamTokenInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamTokensTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamTokens::route('/'),
            'create' => CreateExamToken::route('/create'),
            'view' => ViewExamToken::route('/{record}'),
            'edit' => EditExamToken::route('/{record}/edit'),
        ];
    }
}
