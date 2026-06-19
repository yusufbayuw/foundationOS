<?php

namespace Modules\Employee\Filament\Resources\PayrollComponents;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Employee\Filament\Resources\PayrollComponents\Pages\CreatePayrollComponent;
use Modules\Employee\Filament\Resources\PayrollComponents\Pages\EditPayrollComponent;
use Modules\Employee\Filament\Resources\PayrollComponents\Pages\ListPayrollComponents;
use Modules\Employee\Filament\Resources\PayrollComponents\Pages\ViewPayrollComponent;
use Modules\Employee\Filament\Resources\PayrollComponents\Schemas\PayrollComponentForm;
use Modules\Employee\Filament\Resources\PayrollComponents\Schemas\PayrollComponentInfolist;
use Modules\Employee\Filament\Resources\PayrollComponents\Tables\PayrollComponentsTable;
use Modules\Employee\Models\PayrollComponent;

class PayrollComponentResource extends LocalizedResource
{
    protected static ?string $model = PayrollComponent::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PayrollComponentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PayrollComponentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PayrollComponentsTable::configure($table);
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
            'index' => ListPayrollComponents::route('/'),
            'create' => CreatePayrollComponent::route('/create'),
            'view' => ViewPayrollComponent::route('/{record}'),
            'edit' => EditPayrollComponent::route('/{record}/edit'),
        ];
    }
}
