<?php

namespace Modules\Helpdesk\Filament\Resources\TicketComments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Helpdesk\Filament\Resources\TicketComments\Pages\CreateTicketComment;
use Modules\Helpdesk\Filament\Resources\TicketComments\Pages\EditTicketComment;
use Modules\Helpdesk\Filament\Resources\TicketComments\Pages\ListTicketComments;
use Modules\Helpdesk\Filament\Resources\TicketComments\Pages\ViewTicketComment;
use Modules\Helpdesk\Filament\Resources\TicketComments\Schemas\TicketCommentForm;
use Modules\Helpdesk\Filament\Resources\TicketComments\Schemas\TicketCommentInfolist;
use Modules\Helpdesk\Filament\Resources\TicketComments\Tables\TicketCommentsTable;
use Modules\Helpdesk\Models\TicketComment;

class TicketCommentResource extends ModuleResource
{
    protected static ?string $model = TicketComment::class;

    public static function form(Schema $schema): Schema
    {
        return TicketCommentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TicketCommentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketCommentsTable::configure($table);
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
            'index' => ListTicketComments::route('/'),
            'create' => CreateTicketComment::route('/create'),
            'view' => ViewTicketComment::route('/{record}'),
            'edit' => EditTicketComment::route('/{record}/edit'),
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
