<?php

namespace Modules\Messaging\Filament\Resources\NotificationPreferences;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Messaging\Filament\Resources\NotificationPreferences\Pages\CreateNotificationPreference;
use Modules\Messaging\Filament\Resources\NotificationPreferences\Pages\EditNotificationPreference;
use Modules\Messaging\Filament\Resources\NotificationPreferences\Pages\ListNotificationPreferences;
use Modules\Messaging\Filament\Resources\NotificationPreferences\Pages\ViewNotificationPreference;
use Modules\Messaging\Filament\Resources\NotificationPreferences\Schemas\NotificationPreferenceForm;
use Modules\Messaging\Filament\Resources\NotificationPreferences\Schemas\NotificationPreferenceInfolist;
use Modules\Messaging\Filament\Resources\NotificationPreferences\Tables\NotificationPreferencesTable;
use Modules\Messaging\Models\NotificationPreference;

class NotificationPreferenceResource extends ModuleResource
{
    protected static ?string $model = NotificationPreference::class;

    public static function form(Schema $schema): Schema
    {
        return NotificationPreferenceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return NotificationPreferenceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationPreferencesTable::configure($table);
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
            'index' => ListNotificationPreferences::route('/'),
            'create' => CreateNotificationPreference::route('/create'),
            'view' => ViewNotificationPreference::route('/{record}'),
            'edit' => EditNotificationPreference::route('/{record}/edit'),
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
