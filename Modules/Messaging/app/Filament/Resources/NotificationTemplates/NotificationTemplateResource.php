<?php

namespace Modules\Messaging\Filament\Resources\NotificationTemplates;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Messaging\Filament\Resources\NotificationTemplates\Pages\CreateNotificationTemplate;
use Modules\Messaging\Filament\Resources\NotificationTemplates\Pages\EditNotificationTemplate;
use Modules\Messaging\Filament\Resources\NotificationTemplates\Pages\ListNotificationTemplates;
use Modules\Messaging\Filament\Resources\NotificationTemplates\Pages\ViewNotificationTemplate;
use Modules\Messaging\Filament\Resources\NotificationTemplates\Schemas\NotificationTemplateForm;
use Modules\Messaging\Filament\Resources\NotificationTemplates\Tables\NotificationTemplatesTable;
use Modules\Messaging\Models\NotificationTemplate;

class NotificationTemplateResource extends ModuleResource
{
    protected static ?string $model = NotificationTemplate::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return NotificationTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationTemplatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotificationTemplates::route('/'),
            'create' => CreateNotificationTemplate::route('/create'),
            'view' => ViewNotificationTemplate::route('/{record}'),
            'edit' => EditNotificationTemplate::route('/{record}/edit'),
        ];
    }
}
