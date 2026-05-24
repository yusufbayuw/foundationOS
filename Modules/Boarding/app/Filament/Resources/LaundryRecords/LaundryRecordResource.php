<?php

namespace Modules\Boarding\Filament\Resources\LaundryRecords;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Boarding\Filament\Resources\LaundryRecords\Pages\CreateLaundryRecord;
use Modules\Boarding\Filament\Resources\LaundryRecords\Pages\EditLaundryRecord;
use Modules\Boarding\Filament\Resources\LaundryRecords\Pages\ListLaundryRecords;
use Modules\Boarding\Filament\Resources\LaundryRecords\Pages\ViewLaundryRecord;
use Modules\Boarding\Filament\Resources\LaundryRecords\Schemas\LaundryRecordForm;
use Modules\Boarding\Filament\Resources\LaundryRecords\Schemas\LaundryRecordInfolist;
use Modules\Boarding\Filament\Resources\LaundryRecords\Tables\LaundryRecordsTable;
use Modules\Boarding\Models\LaundryRecord;
use Modules\Core\Filament\Support\ModuleResource;

class LaundryRecordResource extends ModuleResource
{
    protected static ?string $model = LaundryRecord::class;

    public static function form(Schema $schema): Schema
    {
        return LaundryRecordForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LaundryRecordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaundryRecordsTable::configure($table);
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
            'index' => ListLaundryRecords::route('/'),
            'create' => CreateLaundryRecord::route('/create'),
            'view' => ViewLaundryRecord::route('/{record}'),
            'edit' => EditLaundryRecord::route('/{record}/edit'),
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
