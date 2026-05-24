<?php

namespace Modules\Helpdesk\Filament\Resources\TicketCategories;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Helpdesk\Filament\Resources\TicketCategories\Pages\CreateTicketCategory;
use Modules\Helpdesk\Filament\Resources\TicketCategories\Pages\EditTicketCategory;
use Modules\Helpdesk\Filament\Resources\TicketCategories\Pages\ListTicketCategories;
use Modules\Helpdesk\Filament\Resources\TicketCategories\Pages\ViewTicketCategory;
use Modules\Helpdesk\Filament\Resources\TicketCategories\Schemas\TicketCategoryForm;
use Modules\Helpdesk\Filament\Resources\TicketCategories\Schemas\TicketCategoryInfolist;
use Modules\Helpdesk\Filament\Resources\TicketCategories\Tables\TicketCategoriesTable;
use Modules\Helpdesk\Models\TicketCategory;

class TicketCategoryResource extends ModuleResource
{
    protected static ?string $model = TicketCategory::class;

    public static function form(Schema $schema): Schema
    {
        return TicketCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TicketCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketCategoriesTable::configure($table);
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
            'index' => ListTicketCategories::route('/'),
            'create' => CreateTicketCategory::route('/create'),
            'view' => ViewTicketCategory::route('/{record}'),
            'edit' => EditTicketCategory::route('/{record}/edit'),
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
