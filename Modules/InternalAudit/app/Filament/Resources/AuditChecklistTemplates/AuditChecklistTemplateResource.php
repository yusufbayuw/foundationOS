<?php

namespace Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\Pages\CreateAuditChecklistTemplate;
use Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\Pages\EditAuditChecklistTemplate;
use Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\Pages\ListAuditChecklistTemplates;
use Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\Pages\ViewAuditChecklistTemplate;
use Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\Schemas\AuditChecklistTemplateForm;
use Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\Schemas\AuditChecklistTemplateInfolist;
use Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\Tables\AuditChecklistTemplatesTable;
use Modules\InternalAudit\Models\AuditChecklistTemplate;

class AuditChecklistTemplateResource extends ModuleResource
{
    protected static ?string $model = AuditChecklistTemplate::class;

    public static function form(Schema $schema): Schema
    {
        return AuditChecklistTemplateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditChecklistTemplateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditChecklistTemplatesTable::configure($table);
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
            'index' => ListAuditChecklistTemplates::route('/'),
            'create' => CreateAuditChecklistTemplate::route('/create'),
            'view' => ViewAuditChecklistTemplate::route('/{record}'),
            'edit' => EditAuditChecklistTemplate::route('/{record}/edit'),
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
