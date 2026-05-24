<?php

namespace Modules\IsoCompliance\Filament\Resources\ControlImplementations;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\IsoCompliance\Filament\Resources\ControlImplementations\Pages\CreateControlImplementation;
use Modules\IsoCompliance\Filament\Resources\ControlImplementations\Pages\EditControlImplementation;
use Modules\IsoCompliance\Filament\Resources\ControlImplementations\Pages\ListControlImplementations;
use Modules\IsoCompliance\Filament\Resources\ControlImplementations\Pages\ViewControlImplementation;
use Modules\IsoCompliance\Filament\Resources\ControlImplementations\Schemas\ControlImplementationForm;
use Modules\IsoCompliance\Filament\Resources\ControlImplementations\Schemas\ControlImplementationInfolist;
use Modules\IsoCompliance\Filament\Resources\ControlImplementations\Tables\ControlImplementationsTable;
use Modules\IsoCompliance\Models\ControlImplementation;

class ControlImplementationResource extends ModuleResource
{
    protected static ?string $model = ControlImplementation::class;

    public static function form(Schema $schema): Schema
    {
        return ControlImplementationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ControlImplementationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ControlImplementationsTable::configure($table);
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
            'index' => ListControlImplementations::route('/'),
            'create' => CreateControlImplementation::route('/create'),
            'view' => ViewControlImplementation::route('/{record}'),
            'edit' => EditControlImplementation::route('/{record}/edit'),
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
