<?php

namespace Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\Pages\CreateSafetyChecklist;
use Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\Pages\EditSafetyChecklist;
use Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\Pages\ListSafetyChecklists;
use Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\Pages\ViewSafetyChecklist;
use Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\Schemas\SafetyChecklistForm;
use Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\Schemas\SafetyChecklistInfolist;
use Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\Tables\SafetyChecklistsTable;
use Modules\PhysicalSecurity\Models\SafetyChecklist;

class SafetyChecklistResource extends ModuleResource
{
    protected static ?string $model = SafetyChecklist::class;

    public static function form(Schema $schema): Schema
    {
        return SafetyChecklistForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SafetyChecklistInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SafetyChecklistsTable::configure($table);
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
            'index' => ListSafetyChecklists::route('/'),
            'create' => CreateSafetyChecklist::route('/create'),
            'view' => ViewSafetyChecklist::route('/{record}'),
            'edit' => EditSafetyChecklist::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
