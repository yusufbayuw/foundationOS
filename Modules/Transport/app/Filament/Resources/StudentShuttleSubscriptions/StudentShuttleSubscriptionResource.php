<?php

namespace Modules\Transport\Filament\Resources\StudentShuttleSubscriptions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Transport\Filament\Resources\StudentShuttleSubscriptions\Pages\CreateStudentShuttleSubscription;
use Modules\Transport\Filament\Resources\StudentShuttleSubscriptions\Pages\EditStudentShuttleSubscription;
use Modules\Transport\Filament\Resources\StudentShuttleSubscriptions\Pages\ListStudentShuttleSubscriptions;
use Modules\Transport\Filament\Resources\StudentShuttleSubscriptions\Pages\ViewStudentShuttleSubscription;
use Modules\Transport\Filament\Resources\StudentShuttleSubscriptions\Schemas\StudentShuttleSubscriptionForm;
use Modules\Transport\Filament\Resources\StudentShuttleSubscriptions\Schemas\StudentShuttleSubscriptionInfolist;
use Modules\Transport\Filament\Resources\StudentShuttleSubscriptions\Tables\StudentShuttleSubscriptionsTable;
use Modules\Transport\Models\StudentShuttleSubscription;

class StudentShuttleSubscriptionResource extends ModuleResource
{
    protected static ?string $model = StudentShuttleSubscription::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StudentShuttleSubscriptionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentShuttleSubscriptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentShuttleSubscriptionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentShuttleSubscriptions::route('/'),
            'create' => CreateStudentShuttleSubscription::route('/create'),
            'view' => ViewStudentShuttleSubscription::route('/{record}'),
            'edit' => EditStudentShuttleSubscription::route('/{record}/edit'),
        ];
    }
}
