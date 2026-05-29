<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Exam\Filament\Resources\ExamDefinitions\Pages\CreateExamDefinition;
use Modules\Exam\Filament\Resources\ExamDefinitions\Pages\EditExamDefinition;
use Modules\Exam\Filament\Resources\ExamDefinitions\Pages\ListExamDefinitions;
use Modules\Exam\Filament\Resources\ExamDefinitions\Pages\ViewExamDefinition;
use Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers\ExamActivityLogsRelationManager;
use Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers\ExamAnalyticsRelationManager;
use Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers\ExamAnswersRelationManager;
use Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers\ExamAttemptsRelationManager;
use Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers\ExamDefinitionQuestionsRelationManager;
use Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers\ExamGradebookExportLogsRelationManager;
use Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers\ExamParticipantsRelationManager;
use Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers\ExamResultsRelationManager;
use Modules\Exam\Filament\Resources\ExamDefinitions\Schemas\ExamDefinitionForm;
use Modules\Exam\Filament\Resources\ExamDefinitions\Schemas\ExamDefinitionInfolist;
use Modules\Exam\Filament\Resources\ExamDefinitions\Tables\ExamDefinitionsTable;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Services\ExamAuthorizationService;

class ExamDefinitionResource extends LocalizedResource
{
    protected static ?string $model = ExamDefinition::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ExamDefinitionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExamDefinitionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamDefinitionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ExamDefinitionQuestionsRelationManager::class,
            ExamParticipantsRelationManager::class,
            ExamAttemptsRelationManager::class,
            ExamResultsRelationManager::class,
            ExamGradebookExportLogsRelationManager::class,
            ExamAnswersRelationManager::class,
            ExamActivityLogsRelationManager::class,
            ExamAnalyticsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamDefinitions::route('/'),
            'create' => CreateExamDefinition::route('/create'),
            'view' => ViewExamDefinition::route('/{record}'),
            'edit' => EditExamDefinition::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        return app(ExamAuthorizationService::class)
            ->scopeAccessibleExams($query, Auth::user());
    }
}
