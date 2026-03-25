<?php

namespace Modules\Core\Filament\Resources\SubscriptionLogs;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\SubscriptionLogs\Pages\CreateSubscriptionLog;
use Modules\Core\Filament\Resources\SubscriptionLogs\Pages\EditSubscriptionLog;
use Modules\Core\Filament\Resources\SubscriptionLogs\Pages\ListSubscriptionLogs;
use Modules\Core\Filament\Resources\SubscriptionLogs\Pages\ViewSubscriptionLog;
use Modules\Core\Filament\Resources\SubscriptionLogs\Schemas\SubscriptionLogForm;
use Modules\Core\Filament\Resources\SubscriptionLogs\Schemas\SubscriptionLogInfolist;
use Modules\Core\Filament\Resources\SubscriptionLogs\Tables\SubscriptionLogsTable;
use Modules\Core\Models\SubscriptionLog;

class SubscriptionLogResource extends LocalizedResource
{
    protected static ?string $model = SubscriptionLog::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SubscriptionLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubscriptionLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscriptionLogsTable::configure($table);
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
            'index' => ListSubscriptionLogs::route('/'),
            'create' => CreateSubscriptionLog::route('/create'),
            'view' => ViewSubscriptionLog::route('/{record}'),
            'edit' => EditSubscriptionLog::route('/{record}/edit'),
        ];
    }
}
