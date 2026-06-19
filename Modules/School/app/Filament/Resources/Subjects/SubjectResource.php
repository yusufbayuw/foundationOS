<?php

namespace Modules\School\Filament\Resources\Subjects;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\School\Filament\Resources\Subjects\Pages\CreateSubject;
use Modules\School\Filament\Resources\Subjects\Pages\EditSubject;
use Modules\School\Filament\Resources\Subjects\Pages\ListSubjects;
use Modules\School\Filament\Resources\Subjects\Pages\ViewSubject;
use Modules\School\Filament\Resources\Subjects\RelationManagers\AssessmentsRelationManager;
use Modules\School\Filament\Resources\Subjects\RelationManagers\SchedulesRelationManager;
use Modules\School\Filament\Resources\Subjects\Schemas\SubjectForm;
use Modules\School\Filament\Resources\Subjects\Schemas\SubjectInfolist;
use Modules\School\Filament\Resources\Subjects\Tables\SubjectsTable;
use Modules\School\Models\Subject;

class SubjectResource extends LocalizedResource
{
    protected static ?string $model = Subject::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SubjectForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubjectInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SchedulesRelationManager::class,
            AssessmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubjects::route('/'),
            'create' => CreateSubject::route('/create'),
            'view' => ViewSubject::route('/{record}'),
            'edit' => EditSubject::route('/{record}/edit'),
        ];
    }
}
