<?php

namespace Modules\Training\Filament\Resources\TrainingPayments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Training\Filament\Resources\TrainingPayments\Pages\CreateTrainingPayment;
use Modules\Training\Filament\Resources\TrainingPayments\Pages\EditTrainingPayment;
use Modules\Training\Filament\Resources\TrainingPayments\Pages\ListTrainingPayments;
use Modules\Training\Filament\Resources\TrainingPayments\Pages\ViewTrainingPayment;
use Modules\Training\Filament\Resources\TrainingPayments\Schemas\TrainingPaymentForm;
use Modules\Training\Filament\Resources\TrainingPayments\Schemas\TrainingPaymentInfolist;
use Modules\Training\Filament\Resources\TrainingPayments\Tables\TrainingPaymentsTable;
use Modules\Training\Models\TrainingPayment;

class TrainingPaymentResource extends ModuleResource
{
    protected static ?string $model = TrainingPayment::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TrainingPaymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainingPaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingPaymentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingPayments::route('/'),
            'create' => CreateTrainingPayment::route('/create'),
            'view' => ViewTrainingPayment::route('/{record}'),
            'edit' => EditTrainingPayment::route('/{record}/edit'),
        ];
    }
}
