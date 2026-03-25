<?php

namespace Modules\School\Filament\Resources\SchoolClasses;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\SchoolClasses\Pages\CreateSchoolClass;
use Modules\School\Filament\Resources\SchoolClasses\Pages\EditSchoolClass;
use Modules\School\Filament\Resources\SchoolClasses\Pages\ListSchoolClasses;
use Modules\School\Filament\Resources\SchoolClasses\Pages\ViewSchoolClass;
use Modules\School\Filament\Resources\SchoolClasses\Schemas\SchoolClassForm;
use Modules\School\Filament\Resources\SchoolClasses\Schemas\SchoolClassInfolist;
use Modules\School\Filament\Resources\SchoolClasses\Tables\SchoolClassesTable;
use Modules\School\Models\SchoolClass;

class SchoolClassResource extends LocalizedResource
{
    protected static ?string $model = SchoolClass::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SchoolClassForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SchoolClassInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolClassesTable::configure($table);
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
            'index' => ListSchoolClasses::route('/'),
            'create' => CreateSchoolClass::route('/create'),
            'view' => ViewSchoolClass::route('/{record}'),
            'edit' => EditSchoolClass::route('/{record}/edit'),
        ];
    }
}
