<?php

namespace Modules\Employee\Filament\Resources\Employees;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Concerns\ConfiguresGlobalSearch;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Employee\Filament\Resources\Employees\Pages\CreateEmployee;
use Modules\Employee\Filament\Resources\Employees\Pages\EditEmployee;
use Modules\Employee\Filament\Resources\Employees\Pages\ListEmployees;
use Modules\Employee\Filament\Resources\Employees\Pages\ViewEmployee;
use Modules\Employee\Filament\Resources\Employees\Schemas\EmployeeForm;
use Modules\Employee\Filament\Resources\Employees\Schemas\EmployeeInfolist;
use Modules\Employee\Filament\Resources\Employees\Tables\EmployeesTable;
use Modules\Employee\Models\Employee;

class EmployeeResource extends LocalizedResource
{
    use ConfiguresGlobalSearch;

    protected static ?string $model = Employee::class;

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static function globalSearchAttributes(): array
    {
        return ['employee_number', 'full_name', 'email'];
    }

    protected static function globalSearchResultDetails(Employee $record): array
    {
        return static::detailStatus($record->employment_status);
    }

    public static function form(Schema $schema): Schema
    {
        return EmployeeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmployeeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'view' => ViewEmployee::route('/{record}'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
}
