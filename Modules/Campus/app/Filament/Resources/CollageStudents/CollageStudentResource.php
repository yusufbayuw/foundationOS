<?php

namespace Modules\Campus\Filament\Resources\CollageStudents;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\CollageStudents\Pages\CreateCollageStudent;
use Modules\Campus\Filament\Resources\CollageStudents\Pages\EditCollageStudent;
use Modules\Campus\Filament\Resources\CollageStudents\Pages\ListCollageStudents;
use Modules\Campus\Filament\Resources\CollageStudents\Pages\ViewCollageStudent;
use Modules\Campus\Filament\Resources\CollageStudents\Schemas\CollageStudentForm;
use Modules\Campus\Filament\Resources\CollageStudents\Schemas\CollageStudentInfolist;
use Modules\Campus\Filament\Resources\CollageStudents\Tables\CollageStudentsTable;
use Modules\Campus\Models\CollageStudent;

class CollageStudentResource extends LocalizedResource
{
    protected static ?string $model = CollageStudent::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CollageStudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CollageStudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CollageStudentsTable::configure($table);
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
            'index' => ListCollageStudents::route('/'),
            'create' => CreateCollageStudent::route('/create'),
            'view' => ViewCollageStudent::route('/{record}'),
            'edit' => EditCollageStudent::route('/{record}/edit'),
        ];
    }
}
