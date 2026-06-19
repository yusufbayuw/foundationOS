<?php

namespace Modules\Campus\Filament\Resources\Faculties;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\Faculties\Pages\CreateFaculty;
use Modules\Campus\Filament\Resources\Faculties\Pages\EditFaculty;
use Modules\Campus\Filament\Resources\Faculties\Pages\ListFaculties;
use Modules\Campus\Filament\Resources\Faculties\Pages\ViewFaculty;
use Modules\Campus\Filament\Resources\Faculties\RelationManagers\StudyProgramsRelationManager;
use Modules\Campus\Filament\Resources\Faculties\Schemas\FacultyForm;
use Modules\Campus\Filament\Resources\Faculties\Schemas\FacultyInfolist;
use Modules\Campus\Filament\Resources\Faculties\Tables\FacultiesTable;
use Modules\Campus\Models\Faculty;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;

class FacultyResource extends LocalizedResource
{
    protected static ?string $model = Faculty::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FacultyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FacultyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FacultiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StudyProgramsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaculties::route('/'),
            'create' => CreateFaculty::route('/create'),
            'view' => ViewFaculty::route('/{record}'),
            'edit' => EditFaculty::route('/{record}/edit'),
        ];
    }
}
