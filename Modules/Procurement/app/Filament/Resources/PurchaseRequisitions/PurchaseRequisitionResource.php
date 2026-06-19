<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\CreatePurchaseRequisition;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\EditPurchaseRequisition;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\ListPurchaseRequisitions;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\ViewPurchaseRequisition;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\RelationManagers\WorkflowInstancesRelationManager;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Schemas\PurchaseRequisitionForm;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Schemas\PurchaseRequisitionInfolist;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Tables\PurchaseRequisitionsTable;
use Modules\Procurement\Models\PurchaseRequisition;

class PurchaseRequisitionResource extends LocalizedResource
{
    protected static ?string $model = PurchaseRequisition::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function form(Schema $schema): Schema
    {
        return PurchaseRequisitionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PurchaseRequisitionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseRequisitionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            WorkflowInstancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseRequisitions::route('/'),
            'create' => CreatePurchaseRequisition::route('/create'),
            'view' => ViewPurchaseRequisition::route('/{record}'),
            'edit' => EditPurchaseRequisition::route('/{record}/edit'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return $record instanceof PurchaseRequisition
            && ! $record->isLockedForMutation()
            && parent::canEdit($record);
    }
}
