<?php

namespace Modules\InternalAudit\Filament\Resources\AuditEngagements;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\InternalAudit\Filament\Resources\AuditEngagements\Pages\CreateAuditEngagement;
use Modules\InternalAudit\Filament\Resources\AuditEngagements\Pages\EditAuditEngagement;
use Modules\InternalAudit\Filament\Resources\AuditEngagements\Pages\ListAuditEngagements;
use Modules\InternalAudit\Filament\Resources\AuditEngagements\Pages\ViewAuditEngagement;
use Modules\InternalAudit\Filament\Resources\AuditEngagements\Schemas\AuditEngagementForm;
use Modules\InternalAudit\Filament\Resources\AuditEngagements\Tables\AuditEngagementsTable;
use Modules\InternalAudit\Models\AuditEngagement;

class AuditEngagementResource extends ModuleResource
{
    protected static ?string $model = AuditEngagement::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AuditEngagementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditEngagementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditEngagements::route('/'),
            'create' => CreateAuditEngagement::route('/create'),
            'view' => ViewAuditEngagement::route('/{record}'),
            'edit' => EditAuditEngagement::route('/{record}/edit'),
        ];
    }
}
