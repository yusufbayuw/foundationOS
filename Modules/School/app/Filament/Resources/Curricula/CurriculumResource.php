<?php

namespace Modules\School\Filament\Resources\Curricula;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\Curricula\Pages\CreateCurriculum;
use Modules\School\Filament\Resources\Curricula\Pages\EditCurriculum;
use Modules\School\Filament\Resources\Curricula\Pages\ListCurricula;
use Modules\School\Filament\Resources\Curricula\Pages\ViewCurriculum;
use Modules\School\Filament\Resources\Curricula\Schemas\CurriculumForm;
use Modules\School\Filament\Resources\Curricula\Schemas\CurriculumInfolist;
use Modules\School\Filament\Resources\Curricula\Tables\CurriculaTable;
use Modules\School\Models\Curriculum;

class CurriculumResource extends LocalizedResource
{
    protected static ?string $model = Curriculum::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CurriculumForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CurriculumInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CurriculaTable::configure($table);
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
            'index' => ListCurricula::route('/'),
            'create' => CreateCurriculum::route('/create'),
            'view' => ViewCurriculum::route('/{record}'),
            'edit' => EditCurriculum::route('/{record}/edit'),
        ];
    }
}
