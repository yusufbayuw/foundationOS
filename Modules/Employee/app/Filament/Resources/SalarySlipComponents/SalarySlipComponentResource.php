<?php

namespace Modules\Employee\Filament\Resources\SalarySlipComponents;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Employee\Filament\Resources\SalarySlipComponents\Pages\CreateSalarySlipComponent;
use Modules\Employee\Filament\Resources\SalarySlipComponents\Pages\EditSalarySlipComponent;
use Modules\Employee\Filament\Resources\SalarySlipComponents\Pages\ListSalarySlipComponents;
use Modules\Employee\Filament\Resources\SalarySlipComponents\Pages\ViewSalarySlipComponent;
use Modules\Employee\Filament\Resources\SalarySlipComponents\Schemas\SalarySlipComponentForm;
use Modules\Employee\Filament\Resources\SalarySlipComponents\Schemas\SalarySlipComponentInfolist;
use Modules\Employee\Filament\Resources\SalarySlipComponents\Tables\SalarySlipComponentsTable;
use Modules\Employee\Models\SalarySlipComponent;

class SalarySlipComponentResource extends LocalizedResource
{
    protected static ?string $model = SalarySlipComponent::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SalarySlipComponentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalarySlipComponentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalarySlipComponentsTable::configure($table);
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
            'index' => ListSalarySlipComponents::route('/'),
            'create' => CreateSalarySlipComponent::route('/create'),
            'view' => ViewSalarySlipComponent::route('/{record}'),
            'edit' => EditSalarySlipComponent::route('/{record}/edit'),
        ];
    }
}
