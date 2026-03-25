<?php

namespace Modules\Employee\Filament\Resources\SalarySlips;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Employee\Filament\Resources\SalarySlips\Pages\CreateSalarySlip;
use Modules\Employee\Filament\Resources\SalarySlips\Pages\EditSalarySlip;
use Modules\Employee\Filament\Resources\SalarySlips\Pages\ListSalarySlips;
use Modules\Employee\Filament\Resources\SalarySlips\Pages\ViewSalarySlip;
use Modules\Employee\Filament\Resources\SalarySlips\Schemas\SalarySlipForm;
use Modules\Employee\Filament\Resources\SalarySlips\Schemas\SalarySlipInfolist;
use Modules\Employee\Filament\Resources\SalarySlips\Tables\SalarySlipsTable;
use Modules\Employee\Models\SalarySlip;

class SalarySlipResource extends LocalizedResource
{
    protected static ?string $model = SalarySlip::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SalarySlipForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalarySlipInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalarySlipsTable::configure($table);
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
            'index' => ListSalarySlips::route('/'),
            'create' => CreateSalarySlip::route('/create'),
            'view' => ViewSalarySlip::route('/{record}'),
            'edit' => EditSalarySlip::route('/{record}/edit'),
        ];
    }
}
