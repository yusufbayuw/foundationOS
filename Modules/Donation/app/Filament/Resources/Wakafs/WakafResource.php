<?php

namespace Modules\Donation\Filament\Resources\Wakafs;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Donation\Filament\Resources\Wakafs\Pages\CreateWakaf;
use Modules\Donation\Filament\Resources\Wakafs\Pages\EditWakaf;
use Modules\Donation\Filament\Resources\Wakafs\Pages\ListWakafs;
use Modules\Donation\Filament\Resources\Wakafs\Pages\ViewWakaf;
use Modules\Donation\Filament\Resources\Wakafs\Schemas\WakafForm;
use Modules\Donation\Filament\Resources\Wakafs\Schemas\WakafInfolist;
use Modules\Donation\Filament\Resources\Wakafs\Tables\WakafsTable;
use Modules\Donation\Models\Wakaf;

class WakafResource extends ModuleResource
{
    protected static ?string $model = Wakaf::class;

    public static function form(Schema $schema): Schema
    {
        return WakafForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WakafInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WakafsTable::configure($table);
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
            'index' => ListWakafs::route('/'),
            'create' => CreateWakaf::route('/create'),
            'view' => ViewWakaf::route('/{record}'),
            'edit' => EditWakaf::route('/{record}/edit'),
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
