<?php

namespace Modules\Event\Filament\Resources\EventCertificates;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Event\Filament\Resources\EventCertificates\Pages\CreateEventCertificate;
use Modules\Event\Filament\Resources\EventCertificates\Pages\EditEventCertificate;
use Modules\Event\Filament\Resources\EventCertificates\Pages\ListEventCertificates;
use Modules\Event\Filament\Resources\EventCertificates\Pages\ViewEventCertificate;
use Modules\Event\Filament\Resources\EventCertificates\Schemas\EventCertificateForm;
use Modules\Event\Filament\Resources\EventCertificates\Schemas\EventCertificateInfolist;
use Modules\Event\Filament\Resources\EventCertificates\Tables\EventCertificatesTable;
use Modules\Event\Models\EventCertificate;

class EventCertificateResource extends ModuleResource
{
    protected static ?string $model = EventCertificate::class;

    public static function form(Schema $schema): Schema
    {
        return EventCertificateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventCertificateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventCertificatesTable::configure($table);
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
            'index' => ListEventCertificates::route('/'),
            'create' => CreateEventCertificate::route('/create'),
            'view' => ViewEventCertificate::route('/{record}'),
            'edit' => EditEventCertificate::route('/{record}/edit'),
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
