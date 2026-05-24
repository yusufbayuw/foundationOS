<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoEvidence;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\IsoCompliance\Filament\Resources\IsoEvidence\Pages\CreateIsoEvidence;
use Modules\IsoCompliance\Filament\Resources\IsoEvidence\Pages\EditIsoEvidence;
use Modules\IsoCompliance\Filament\Resources\IsoEvidence\Pages\ListIsoEvidence;
use Modules\IsoCompliance\Filament\Resources\IsoEvidence\Pages\ViewIsoEvidence;
use Modules\IsoCompliance\Filament\Resources\IsoEvidence\Schemas\IsoEvidenceForm;
use Modules\IsoCompliance\Filament\Resources\IsoEvidence\Schemas\IsoEvidenceInfolist;
use Modules\IsoCompliance\Filament\Resources\IsoEvidence\Tables\IsoEvidenceTable;
use Modules\IsoCompliance\Models\IsoEvidence;

class IsoEvidenceResource extends ModuleResource
{
    protected static ?string $model = IsoEvidence::class;

    public static function form(Schema $schema): Schema
    {
        return IsoEvidenceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IsoEvidenceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IsoEvidenceTable::configure($table);
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
            'index' => ListIsoEvidence::route('/'),
            'create' => CreateIsoEvidence::route('/create'),
            'view' => ViewIsoEvidence::route('/{record}'),
            'edit' => EditIsoEvidence::route('/{record}/edit'),
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
