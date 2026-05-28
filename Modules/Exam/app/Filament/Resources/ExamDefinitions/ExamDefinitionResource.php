<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Exam\Filament\Resources\ExamDefinitions\Pages\CreateExamDefinition;
use Modules\Exam\Filament\Resources\ExamDefinitions\Pages\EditExamDefinition;
use Modules\Exam\Filament\Resources\ExamDefinitions\Pages\ListExamDefinitions;
use Modules\Exam\Filament\Resources\ExamDefinitions\Pages\ViewExamDefinition;
use Modules\Exam\Filament\Resources\ExamDefinitions\Schemas\ExamDefinitionForm;
use Modules\Exam\Filament\Resources\ExamDefinitions\Schemas\ExamDefinitionInfolist;
use Modules\Exam\Filament\Resources\ExamDefinitions\Tables\ExamDefinitionsTable;
use Modules\Exam\Models\ExamDefinition;

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

    public static function getPages(): array
    {
        return [
            'index' => ListExamDefinitions::route('/'),
            'create' => CreateExamDefinition::route('/create'),
            'view' => ViewExamDefinition::route('/{record}'),
            'edit' => EditExamDefinition::route('/{record}/edit'),
        ];
    }
}
