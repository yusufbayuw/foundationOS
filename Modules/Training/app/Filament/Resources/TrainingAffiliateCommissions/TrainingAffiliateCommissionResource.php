<?php

namespace Modules\Training\Filament\Resources\TrainingAffiliateCommissions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Training\Filament\Resources\TrainingAffiliateCommissions\Pages\CreateTrainingAffiliateCommission;
use Modules\Training\Filament\Resources\TrainingAffiliateCommissions\Pages\EditTrainingAffiliateCommission;
use Modules\Training\Filament\Resources\TrainingAffiliateCommissions\Pages\ListTrainingAffiliateCommissions;
use Modules\Training\Filament\Resources\TrainingAffiliateCommissions\Pages\ViewTrainingAffiliateCommission;
use Modules\Training\Filament\Resources\TrainingAffiliateCommissions\Schemas\TrainingAffiliateCommissionForm;
use Modules\Training\Filament\Resources\TrainingAffiliateCommissions\Schemas\TrainingAffiliateCommissionInfolist;
use Modules\Training\Filament\Resources\TrainingAffiliateCommissions\Tables\TrainingAffiliateCommissionsTable;
use Modules\Training\Models\TrainingAffiliateCommission;

class TrainingAffiliateCommissionResource extends ModuleResource
{
    protected static ?string $model = TrainingAffiliateCommission::class;

    protected static ?string $recordTitleAttribute = 'status';

    public static function form(Schema $schema): Schema
    {
        return TrainingAffiliateCommissionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainingAffiliateCommissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingAffiliateCommissionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingAffiliateCommissions::route('/'),
            'create' => CreateTrainingAffiliateCommission::route('/create'),
            'view' => ViewTrainingAffiliateCommission::route('/{record}'),
            'edit' => EditTrainingAffiliateCommission::route('/{record}/edit'),
        ];
    }
}
