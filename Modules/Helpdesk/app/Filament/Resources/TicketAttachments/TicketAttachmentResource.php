<?php

namespace Modules\Helpdesk\Filament\Resources\TicketAttachments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Helpdesk\Filament\Resources\TicketAttachments\Pages\CreateTicketAttachment;
use Modules\Helpdesk\Filament\Resources\TicketAttachments\Pages\EditTicketAttachment;
use Modules\Helpdesk\Filament\Resources\TicketAttachments\Pages\ListTicketAttachments;
use Modules\Helpdesk\Filament\Resources\TicketAttachments\Pages\ViewTicketAttachment;
use Modules\Helpdesk\Filament\Resources\TicketAttachments\Schemas\TicketAttachmentForm;
use Modules\Helpdesk\Filament\Resources\TicketAttachments\Schemas\TicketAttachmentInfolist;
use Modules\Helpdesk\Filament\Resources\TicketAttachments\Tables\TicketAttachmentsTable;
use Modules\Helpdesk\Models\TicketAttachment;

class TicketAttachmentResource extends ModuleResource
{
    protected static ?string $model = TicketAttachment::class;

    public static function form(Schema $schema): Schema
    {
        return TicketAttachmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TicketAttachmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketAttachmentsTable::configure($table);
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
            'index' => ListTicketAttachments::route('/'),
            'create' => CreateTicketAttachment::route('/create'),
            'view' => ViewTicketAttachment::route('/{record}'),
            'edit' => EditTicketAttachment::route('/{record}/edit'),
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
