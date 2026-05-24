<?php

namespace Modules\InternalAudit\Filament\Resources\AuditFindings;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\InternalAudit\Filament\Resources\AuditFindings\Pages\CreateAuditFinding;
use Modules\InternalAudit\Filament\Resources\AuditFindings\Pages\EditAuditFinding;
use Modules\InternalAudit\Filament\Resources\AuditFindings\Pages\ListAuditFindings;
use Modules\InternalAudit\Filament\Resources\AuditFindings\Pages\ViewAuditFinding;
use Modules\InternalAudit\Filament\Resources\AuditFindings\Schemas\AuditFindingForm;
use Modules\InternalAudit\Filament\Resources\AuditFindings\Schemas\AuditFindingInfolist;
use Modules\InternalAudit\Filament\Resources\AuditFindings\Tables\AuditFindingsTable;
use Modules\InternalAudit\Models\AuditFinding;

class AuditFindingResource extends ModuleResource
{
    protected static ?string $model = AuditFinding::class;

    public static function form(Schema $schema): Schema
    {
        return AuditFindingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditFindingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditFindingsTable::configure($table);
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
            'index' => ListAuditFindings::route('/'),
            'create' => CreateAuditFinding::route('/create'),
            'view' => ViewAuditFinding::route('/{record}'),
            'edit' => EditAuditFinding::route('/{record}/edit'),
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
