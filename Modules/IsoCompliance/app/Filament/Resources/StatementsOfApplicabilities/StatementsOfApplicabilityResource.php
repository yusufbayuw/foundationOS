<?php

namespace Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\Pages\CreateStatementsOfApplicability;
use Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\Pages\EditStatementsOfApplicability;
use Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\Pages\ListStatementsOfApplicabilities;
use Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\Pages\ViewStatementsOfApplicability;
use Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\Schemas\StatementsOfApplicabilityForm;
use Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\Schemas\StatementsOfApplicabilityInfolist;
use Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\Tables\StatementsOfApplicabilitiesTable;
use Modules\IsoCompliance\Models\StatementsOfApplicability;

class StatementsOfApplicabilityResource extends ModuleResource
{
    protected static ?string $model = StatementsOfApplicability::class;

    public static function form(Schema $schema): Schema
    {
        return StatementsOfApplicabilityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StatementsOfApplicabilityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StatementsOfApplicabilitiesTable::configure($table);
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
            'index' => ListStatementsOfApplicabilities::route('/'),
            'create' => CreateStatementsOfApplicability::route('/create'),
            'view' => ViewStatementsOfApplicability::route('/{record}'),
            'edit' => EditStatementsOfApplicability::route('/{record}/edit'),
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
