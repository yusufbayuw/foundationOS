<?php

namespace Modules\Property\Filament\Resources\LeaseAgreements;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Property\Filament\Resources\LeaseAgreements\Pages\CreateLeaseAgreement;
use Modules\Property\Filament\Resources\LeaseAgreements\Pages\EditLeaseAgreement;
use Modules\Property\Filament\Resources\LeaseAgreements\Pages\ListLeaseAgreements;
use Modules\Property\Filament\Resources\LeaseAgreements\Pages\ViewLeaseAgreement;
use Modules\Property\Filament\Resources\LeaseAgreements\Schemas\LeaseAgreementForm;
use Modules\Property\Filament\Resources\LeaseAgreements\Schemas\LeaseAgreementInfolist;
use Modules\Property\Filament\Resources\LeaseAgreements\Tables\LeaseAgreementsTable;
use Modules\Property\Models\LeaseAgreement;

class LeaseAgreementResource extends ModuleResource
{
    protected static ?string $model = LeaseAgreement::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return LeaseAgreementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeaseAgreementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaseAgreementsTable::configure($table);
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
            'index' => ListLeaseAgreements::route('/'),
            'create' => CreateLeaseAgreement::route('/create'),
            'view' => ViewLeaseAgreement::route('/{record}'),
            'edit' => EditLeaseAgreement::route('/{record}/edit'),
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
