<?php

namespace Modules\InternalAudit\Filament\Resources\AuditPrograms;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\InternalAudit\Filament\Resources\AuditPrograms\Pages\CreateAuditProgram;
use Modules\InternalAudit\Filament\Resources\AuditPrograms\Pages\EditAuditProgram;
use Modules\InternalAudit\Filament\Resources\AuditPrograms\Pages\ListAuditPrograms;
use Modules\InternalAudit\Filament\Resources\AuditPrograms\Pages\ViewAuditProgram;
use Modules\InternalAudit\Filament\Resources\AuditPrograms\Schemas\AuditProgramForm;
use Modules\InternalAudit\Filament\Resources\AuditPrograms\Schemas\AuditProgramInfolist;
use Modules\InternalAudit\Filament\Resources\AuditPrograms\Tables\AuditProgramsTable;
use Modules\InternalAudit\Models\AuditProgram;

class AuditProgramResource extends ModuleResource
{
    protected static ?string $model = AuditProgram::class;

    public static function form(Schema $schema): Schema
    {
        return AuditProgramForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditProgramInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditProgramsTable::configure($table);
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
            'index' => ListAuditPrograms::route('/'),
            'create' => CreateAuditProgram::route('/create'),
            'view' => ViewAuditProgram::route('/{record}'),
            'edit' => EditAuditProgram::route('/{record}/edit'),
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
