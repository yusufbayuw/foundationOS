<?php

namespace Modules\Training\Filament\Resources\TrainingEnrollments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Training\Filament\Resources\TrainingEnrollments\Pages\CreateTrainingEnrollment;
use Modules\Training\Filament\Resources\TrainingEnrollments\Pages\EditTrainingEnrollment;
use Modules\Training\Filament\Resources\TrainingEnrollments\Pages\ListTrainingEnrollments;
use Modules\Training\Filament\Resources\TrainingEnrollments\Pages\ViewTrainingEnrollment;
use Modules\Training\Filament\Resources\TrainingEnrollments\Schemas\TrainingEnrollmentForm;
use Modules\Training\Filament\Resources\TrainingEnrollments\Schemas\TrainingEnrollmentInfolist;
use Modules\Training\Filament\Resources\TrainingEnrollments\Tables\TrainingEnrollmentsTable;
use Modules\Training\Models\TrainingEnrollment;

class TrainingEnrollmentResource extends ModuleResource
{
    protected static ?string $model = TrainingEnrollment::class;

    protected static ?string $recordTitleAttribute = 'status';

    public static function form(Schema $schema): Schema
    {
        return TrainingEnrollmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainingEnrollmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingEnrollmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingEnrollments::route('/'),
            'create' => CreateTrainingEnrollment::route('/create'),
            'view' => ViewTrainingEnrollment::route('/{record}'),
            'edit' => EditTrainingEnrollment::route('/{record}/edit'),
        ];
    }
}
