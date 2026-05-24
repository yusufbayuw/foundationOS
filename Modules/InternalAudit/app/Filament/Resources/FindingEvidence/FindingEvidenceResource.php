<?php

namespace Modules\InternalAudit\Filament\Resources\FindingEvidence;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\InternalAudit\Filament\Resources\FindingEvidence\Pages\CreateFindingEvidence;
use Modules\InternalAudit\Filament\Resources\FindingEvidence\Pages\EditFindingEvidence;
use Modules\InternalAudit\Filament\Resources\FindingEvidence\Pages\ListFindingEvidence;
use Modules\InternalAudit\Filament\Resources\FindingEvidence\Pages\ViewFindingEvidence;
use Modules\InternalAudit\Filament\Resources\FindingEvidence\Schemas\FindingEvidenceForm;
use Modules\InternalAudit\Filament\Resources\FindingEvidence\Schemas\FindingEvidenceInfolist;
use Modules\InternalAudit\Filament\Resources\FindingEvidence\Tables\FindingEvidenceTable;
use Modules\InternalAudit\Models\FindingEvidence;

class FindingEvidenceResource extends ModuleResource
{
    protected static ?string $model = FindingEvidence::class;

    public static function form(Schema $schema): Schema
    {
        return FindingEvidenceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FindingEvidenceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FindingEvidenceTable::configure($table);
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
            'index' => ListFindingEvidence::route('/'),
            'create' => CreateFindingEvidence::route('/create'),
            'view' => ViewFindingEvidence::route('/{record}'),
            'edit' => EditFindingEvidence::route('/{record}/edit'),
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
