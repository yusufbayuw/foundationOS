<?php

namespace Modules\Donation\Filament\Resources\Endowments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Donation\Filament\Resources\Endowments\Pages\CreateEndowment;
use Modules\Donation\Filament\Resources\Endowments\Pages\EditEndowment;
use Modules\Donation\Filament\Resources\Endowments\Pages\ListEndowments;
use Modules\Donation\Filament\Resources\Endowments\Pages\ViewEndowment;
use Modules\Donation\Filament\Resources\Endowments\Schemas\EndowmentForm;
use Modules\Donation\Filament\Resources\Endowments\Schemas\EndowmentInfolist;
use Modules\Donation\Filament\Resources\Endowments\Tables\EndowmentsTable;
use Modules\Donation\Models\Endowment;

class EndowmentResource extends ModuleResource
{
    protected static ?string $model = Endowment::class;

    public static function form(Schema $schema): Schema
    {
        return EndowmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EndowmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EndowmentsTable::configure($table);
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
            'index' => ListEndowments::route('/'),
            'create' => CreateEndowment::route('/create'),
            'view' => ViewEndowment::route('/{record}'),
            'edit' => EditEndowment::route('/{record}/edit'),
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
