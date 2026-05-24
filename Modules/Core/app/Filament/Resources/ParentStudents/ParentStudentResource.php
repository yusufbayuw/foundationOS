<?php

namespace Modules\Core\Filament\Resources\ParentStudents;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\ParentStudents\Pages\CreateParentStudent;
use Modules\Core\Filament\Resources\ParentStudents\Pages\EditParentStudent;
use Modules\Core\Filament\Resources\ParentStudents\Pages\ListParentStudents;
use Modules\Core\Filament\Resources\ParentStudents\Pages\ViewParentStudent;
use Modules\Core\Filament\Resources\ParentStudents\Schemas\ParentStudentForm;
use Modules\Core\Filament\Resources\ParentStudents\Schemas\ParentStudentInfolist;
use Modules\Core\Filament\Resources\ParentStudents\Tables\ParentStudentsTable;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Core\Models\ParentStudent;

class ParentStudentResource extends ModuleResource
{
    protected static ?string $model = ParentStudent::class;

    public static function form(Schema $schema): Schema
    {
        return ParentStudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ParentStudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParentStudentsTable::configure($table);
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
            'index' => ListParentStudents::route('/'),
            'create' => CreateParentStudent::route('/create'),
            'view' => ViewParentStudent::route('/{record}'),
            'edit' => EditParentStudent::route('/{record}/edit'),
        ];
    }
}
