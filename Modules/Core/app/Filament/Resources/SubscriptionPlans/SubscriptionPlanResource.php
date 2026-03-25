<?php

namespace Modules\Core\Filament\Resources\SubscriptionPlans;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\SubscriptionPlans\Pages\CreateSubscriptionPlan;
use Modules\Core\Filament\Resources\SubscriptionPlans\Pages\EditSubscriptionPlan;
use Modules\Core\Filament\Resources\SubscriptionPlans\Pages\ListSubscriptionPlans;
use Modules\Core\Filament\Resources\SubscriptionPlans\Pages\ViewSubscriptionPlan;
use Modules\Core\Filament\Resources\SubscriptionPlans\Schemas\SubscriptionPlanForm;
use Modules\Core\Filament\Resources\SubscriptionPlans\Schemas\SubscriptionPlanInfolist;
use Modules\Core\Filament\Resources\SubscriptionPlans\Tables\SubscriptionPlansTable;
use Modules\Core\Models\SubscriptionPlan;

class SubscriptionPlanResource extends LocalizedResource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SubscriptionPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubscriptionPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscriptionPlansTable::configure($table);
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
            'index' => ListSubscriptionPlans::route('/'),
            'create' => CreateSubscriptionPlan::route('/create'),
            'view' => ViewSubscriptionPlan::route('/{record}'),
            'edit' => EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
