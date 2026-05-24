<?php

namespace Modules\Clinic\Filament\Resources\HealthRecords;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Clinic\Filament\Resources\HealthRecords\Pages\CreateHealthRecord;
use Modules\Clinic\Filament\Resources\HealthRecords\Pages\EditHealthRecord;
use Modules\Clinic\Filament\Resources\HealthRecords\Pages\ListHealthRecords;
use Modules\Clinic\Filament\Resources\HealthRecords\Pages\ViewHealthRecord;
use Modules\Clinic\Filament\Resources\HealthRecords\Schemas\HealthRecordForm;
use Modules\Clinic\Filament\Resources\HealthRecords\Schemas\HealthRecordInfolist;
use Modules\Clinic\Filament\Resources\HealthRecords\Tables\HealthRecordsTable;
use Modules\Clinic\Models\HealthRecord;
use Modules\Core\Filament\Support\ModuleResource;

class HealthRecordResource extends ModuleResource
{
    protected static ?string $model = HealthRecord::class;

    public static function form(Schema $schema): Schema
    {
        return HealthRecordForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HealthRecordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HealthRecordsTable::configure($table);
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
            'index' => ListHealthRecords::route('/'),
            'create' => CreateHealthRecord::route('/create'),
            'view' => ViewHealthRecord::route('/{record}'),
            'edit' => EditHealthRecord::route('/{record}/edit'),
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
