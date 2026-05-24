<?php

namespace Modules\ItOps\Filament\Resources\IpAddressRecords;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\ItOps\Filament\Resources\IpAddressRecords\Pages\CreateIpAddressRecord;
use Modules\ItOps\Filament\Resources\IpAddressRecords\Pages\EditIpAddressRecord;
use Modules\ItOps\Filament\Resources\IpAddressRecords\Pages\ListIpAddressRecords;
use Modules\ItOps\Filament\Resources\IpAddressRecords\Pages\ViewIpAddressRecord;
use Modules\ItOps\Filament\Resources\IpAddressRecords\Schemas\IpAddressRecordForm;
use Modules\ItOps\Filament\Resources\IpAddressRecords\Schemas\IpAddressRecordInfolist;
use Modules\ItOps\Filament\Resources\IpAddressRecords\Tables\IpAddressRecordsTable;
use Modules\ItOps\Models\IpAddressRecord;

class IpAddressRecordResource extends ModuleResource
{
    protected static ?string $model = IpAddressRecord::class;

    public static function form(Schema $schema): Schema
    {
        return IpAddressRecordForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IpAddressRecordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IpAddressRecordsTable::configure($table);
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
            'index' => ListIpAddressRecords::route('/'),
            'create' => CreateIpAddressRecord::route('/create'),
            'view' => ViewIpAddressRecord::route('/{record}'),
            'edit' => EditIpAddressRecord::route('/{record}/edit'),
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
