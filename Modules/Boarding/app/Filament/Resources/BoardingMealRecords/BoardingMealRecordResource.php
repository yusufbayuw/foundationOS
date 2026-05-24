<?php

namespace Modules\Boarding\Filament\Resources\BoardingMealRecords;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Boarding\Filament\Resources\BoardingMealRecords\Pages\CreateBoardingMealRecord;
use Modules\Boarding\Filament\Resources\BoardingMealRecords\Pages\EditBoardingMealRecord;
use Modules\Boarding\Filament\Resources\BoardingMealRecords\Pages\ListBoardingMealRecords;
use Modules\Boarding\Filament\Resources\BoardingMealRecords\Pages\ViewBoardingMealRecord;
use Modules\Boarding\Filament\Resources\BoardingMealRecords\Schemas\BoardingMealRecordForm;
use Modules\Boarding\Filament\Resources\BoardingMealRecords\Schemas\BoardingMealRecordInfolist;
use Modules\Boarding\Filament\Resources\BoardingMealRecords\Tables\BoardingMealRecordsTable;
use Modules\Boarding\Models\BoardingMealRecord;
use Modules\Core\Filament\Support\ModuleResource;

class BoardingMealRecordResource extends ModuleResource
{
    protected static ?string $model = BoardingMealRecord::class;

    public static function form(Schema $schema): Schema
    {
        return BoardingMealRecordForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BoardingMealRecordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BoardingMealRecordsTable::configure($table);
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
            'index' => ListBoardingMealRecords::route('/'),
            'create' => CreateBoardingMealRecord::route('/create'),
            'view' => ViewBoardingMealRecord::route('/{record}'),
            'edit' => EditBoardingMealRecord::route('/{record}/edit'),
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
