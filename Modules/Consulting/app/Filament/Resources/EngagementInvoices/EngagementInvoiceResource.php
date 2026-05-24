<?php

namespace Modules\Consulting\Filament\Resources\EngagementInvoices;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Consulting\Filament\Resources\EngagementInvoices\Pages\CreateEngagementInvoice;
use Modules\Consulting\Filament\Resources\EngagementInvoices\Pages\EditEngagementInvoice;
use Modules\Consulting\Filament\Resources\EngagementInvoices\Pages\ListEngagementInvoices;
use Modules\Consulting\Filament\Resources\EngagementInvoices\Pages\ViewEngagementInvoice;
use Modules\Consulting\Filament\Resources\EngagementInvoices\Schemas\EngagementInvoiceForm;
use Modules\Consulting\Filament\Resources\EngagementInvoices\Schemas\EngagementInvoiceInfolist;
use Modules\Consulting\Filament\Resources\EngagementInvoices\Tables\EngagementInvoicesTable;
use Modules\Consulting\Models\EngagementInvoice;
use Modules\Core\Filament\Support\ModuleResource;

class EngagementInvoiceResource extends ModuleResource
{
    protected static ?string $model = EngagementInvoice::class;

    public static function form(Schema $schema): Schema
    {
        return EngagementInvoiceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EngagementInvoiceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EngagementInvoicesTable::configure($table);
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
            'index' => ListEngagementInvoices::route('/'),
            'create' => CreateEngagementInvoice::route('/create'),
            'view' => ViewEngagementInvoice::route('/{record}'),
            'edit' => EditEngagementInvoice::route('/{record}/edit'),
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
