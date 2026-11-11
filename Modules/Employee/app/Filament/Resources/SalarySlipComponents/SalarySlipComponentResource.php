<?php

namespace Modules\Employee\Filament\Resources\SalarySlipComponents;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
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

    // Component lines are generated from the payroll calculation service.
    // The resource remains viewable, but cannot bypass the slip lifecycle.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canForceDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canForceDeleteAny(): bool
    {
        return false;
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
