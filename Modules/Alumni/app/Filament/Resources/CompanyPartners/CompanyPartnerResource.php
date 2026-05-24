<?php

namespace Modules\Alumni\Filament\Resources\CompanyPartners;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Alumni\Filament\Resources\CompanyPartners\Pages\CreateCompanyPartner;
use Modules\Alumni\Filament\Resources\CompanyPartners\Pages\EditCompanyPartner;
use Modules\Alumni\Filament\Resources\CompanyPartners\Pages\ListCompanyPartners;
use Modules\Alumni\Filament\Resources\CompanyPartners\Pages\ViewCompanyPartner;
use Modules\Alumni\Filament\Resources\CompanyPartners\Schemas\CompanyPartnerForm;
use Modules\Alumni\Filament\Resources\CompanyPartners\Schemas\CompanyPartnerInfolist;
use Modules\Alumni\Filament\Resources\CompanyPartners\Tables\CompanyPartnersTable;
use Modules\Alumni\Models\CompanyPartner;
use Modules\Core\Filament\Support\ModuleResource;

class CompanyPartnerResource extends ModuleResource
{
    protected static ?string $model = CompanyPartner::class;

    public static function form(Schema $schema): Schema
    {
        return CompanyPartnerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompanyPartnerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyPartnersTable::configure($table);
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
            'index' => ListCompanyPartners::route('/'),
            'create' => CreateCompanyPartner::route('/create'),
            'view' => ViewCompanyPartner::route('/{record}'),
            'edit' => EditCompanyPartner::route('/{record}/edit'),
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
