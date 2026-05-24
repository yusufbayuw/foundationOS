<?php

namespace Modules\Enrollment\Filament\Resources\LeadActivities;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Enrollment\Filament\Resources\LeadActivities\Pages\CreateLeadActivity;
use Modules\Enrollment\Filament\Resources\LeadActivities\Pages\EditLeadActivity;
use Modules\Enrollment\Filament\Resources\LeadActivities\Pages\ListLeadActivities;
use Modules\Enrollment\Filament\Resources\LeadActivities\Pages\ViewLeadActivity;
use Modules\Enrollment\Filament\Resources\LeadActivities\Schemas\LeadActivityForm;
use Modules\Enrollment\Filament\Resources\LeadActivities\Schemas\LeadActivityInfolist;
use Modules\Enrollment\Filament\Resources\LeadActivities\Tables\LeadActivitiesTable;
use Modules\Enrollment\Models\LeadActivity;

class LeadActivityResource extends ModuleResource
{
    protected static ?string $model = LeadActivity::class;

    public static function form(Schema $schema): Schema
    {
        return LeadActivityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeadActivityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadActivitiesTable::configure($table);
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
            'index' => ListLeadActivities::route('/'),
            'create' => CreateLeadActivity::route('/create'),
            'view' => ViewLeadActivity::route('/{record}'),
            'edit' => EditLeadActivity::route('/{record}/edit'),
        ];
    }
}
