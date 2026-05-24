<?php

namespace Modules\Consulting\Filament\Resources\ConsultingClients;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Consulting\Filament\Resources\ConsultingClients\Pages\CreateConsultingClient;
use Modules\Consulting\Filament\Resources\ConsultingClients\Pages\EditConsultingClient;
use Modules\Consulting\Filament\Resources\ConsultingClients\Pages\ListConsultingClients;
use Modules\Consulting\Filament\Resources\ConsultingClients\Pages\ViewConsultingClient;
use Modules\Consulting\Filament\Resources\ConsultingClients\Schemas\ConsultingClientForm;
use Modules\Consulting\Filament\Resources\ConsultingClients\Schemas\ConsultingClientInfolist;
use Modules\Consulting\Filament\Resources\ConsultingClients\Tables\ConsultingClientsTable;
use Modules\Consulting\Models\ConsultingClient;
use Modules\Core\Filament\Support\ModuleResource;

class ConsultingClientResource extends ModuleResource
{
    protected static ?string $model = ConsultingClient::class;

    public static function form(Schema $schema): Schema
    {
        return ConsultingClientForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ConsultingClientInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsultingClientsTable::configure($table);
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
            'index' => ListConsultingClients::route('/'),
            'create' => CreateConsultingClient::route('/create'),
            'view' => ViewConsultingClient::route('/{record}'),
            'edit' => EditConsultingClient::route('/{record}/edit'),
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
