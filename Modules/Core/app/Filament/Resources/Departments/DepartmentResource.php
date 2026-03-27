<?php

namespace Modules\Core\Filament\Resources\Departments;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Departments\Pages\CreateDepartment;
use Modules\Core\Filament\Resources\Departments\Pages\EditDepartment;
use Modules\Core\Filament\Resources\Departments\Pages\ListDepartments;
use Modules\Core\Filament\Resources\Departments\Pages\ViewDepartment;
use Modules\Core\Filament\Resources\Departments\RelationManagers\SchoolClassesRelationManager;
use Modules\Core\Filament\Resources\Departments\Schemas\DepartmentForm;
use Modules\Core\Filament\Resources\Departments\Schemas\DepartmentInfolist;
use Modules\Core\Filament\Resources\Departments\Tables\DepartmentsTable;
use Modules\Core\Models\Department;

class DepartmentResource extends LocalizedResource
{
    protected static ?string $model = Department::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DepartmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DepartmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepartmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SchoolClassesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'view' => ViewDepartment::route('/{record}'),
            'edit' => EditDepartment::route('/{record}/edit'),
        ];
    }
}
