<?php

namespace Modules\Employee\Filament\Resources\EmploymentContracts;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Employee\Filament\Resources\EmploymentContracts\Pages\CreateEmploymentContract;
use Modules\Employee\Filament\Resources\EmploymentContracts\Pages\EditEmploymentContract;
use Modules\Employee\Filament\Resources\EmploymentContracts\Pages\ListEmploymentContracts;
use Modules\Employee\Filament\Resources\EmploymentContracts\Pages\ViewEmploymentContract;
use Modules\Employee\Filament\Resources\EmploymentContracts\Schemas\EmploymentContractForm;
use Modules\Employee\Filament\Resources\EmploymentContracts\Schemas\EmploymentContractInfolist;
use Modules\Employee\Filament\Resources\EmploymentContracts\Tables\EmploymentContractsTable;
use Modules\Employee\Models\EmploymentContract;

class EmploymentContractResource extends LocalizedResource
{
    protected static ?string $model = EmploymentContract::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return EmploymentContractForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmploymentContractInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmploymentContractsTable::configure($table);
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
            'index' => ListEmploymentContracts::route('/'),
            'create' => CreateEmploymentContract::route('/create'),
            'view' => ViewEmploymentContract::route('/{record}'),
            'edit' => EditEmploymentContract::route('/{record}/edit'),
        ];
    }
}
