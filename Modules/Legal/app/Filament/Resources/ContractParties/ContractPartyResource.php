<?php

namespace Modules\Legal\Filament\Resources\ContractParties;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Legal\Filament\Resources\ContractParties\Pages\CreateContractParty;
use Modules\Legal\Filament\Resources\ContractParties\Pages\EditContractParty;
use Modules\Legal\Filament\Resources\ContractParties\Pages\ListContractParties;
use Modules\Legal\Filament\Resources\ContractParties\Pages\ViewContractParty;
use Modules\Legal\Filament\Resources\ContractParties\Schemas\ContractPartyForm;
use Modules\Legal\Filament\Resources\ContractParties\Schemas\ContractPartyInfolist;
use Modules\Legal\Filament\Resources\ContractParties\Tables\ContractPartiesTable;
use Modules\Legal\Models\ContractParty;

class ContractPartyResource extends ModuleResource
{
    protected static ?string $model = ContractParty::class;

    public static function form(Schema $schema): Schema
    {
        return ContractPartyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContractPartyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContractPartiesTable::configure($table);
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
            'index' => ListContractParties::route('/'),
            'create' => CreateContractParty::route('/create'),
            'view' => ViewContractParty::route('/{record}'),
            'edit' => EditContractParty::route('/{record}/edit'),
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
