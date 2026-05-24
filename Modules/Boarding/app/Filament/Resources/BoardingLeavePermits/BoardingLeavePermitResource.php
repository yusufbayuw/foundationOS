<?php

namespace Modules\Boarding\Filament\Resources\BoardingLeavePermits;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Boarding\Filament\Resources\BoardingLeavePermits\Pages\CreateBoardingLeavePermit;
use Modules\Boarding\Filament\Resources\BoardingLeavePermits\Pages\EditBoardingLeavePermit;
use Modules\Boarding\Filament\Resources\BoardingLeavePermits\Pages\ListBoardingLeavePermits;
use Modules\Boarding\Filament\Resources\BoardingLeavePermits\Pages\ViewBoardingLeavePermit;
use Modules\Boarding\Filament\Resources\BoardingLeavePermits\Schemas\BoardingLeavePermitForm;
use Modules\Boarding\Filament\Resources\BoardingLeavePermits\Schemas\BoardingLeavePermitInfolist;
use Modules\Boarding\Filament\Resources\BoardingLeavePermits\Tables\BoardingLeavePermitsTable;
use Modules\Boarding\Models\BoardingLeavePermit;
use Modules\Core\Filament\Support\ModuleResource;

class BoardingLeavePermitResource extends ModuleResource
{
    protected static ?string $model = BoardingLeavePermit::class;

    public static function form(Schema $schema): Schema
    {
        return BoardingLeavePermitForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BoardingLeavePermitInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BoardingLeavePermitsTable::configure($table);
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
            'index' => ListBoardingLeavePermits::route('/'),
            'create' => CreateBoardingLeavePermit::route('/create'),
            'view' => ViewBoardingLeavePermit::route('/{record}'),
            'edit' => EditBoardingLeavePermit::route('/{record}/edit'),
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
