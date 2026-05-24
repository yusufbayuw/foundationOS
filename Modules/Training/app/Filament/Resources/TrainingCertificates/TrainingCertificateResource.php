<?php

namespace Modules\Training\Filament\Resources\TrainingCertificates;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Training\Filament\Resources\TrainingCertificates\Pages\CreateTrainingCertificate;
use Modules\Training\Filament\Resources\TrainingCertificates\Pages\EditTrainingCertificate;
use Modules\Training\Filament\Resources\TrainingCertificates\Pages\ListTrainingCertificates;
use Modules\Training\Filament\Resources\TrainingCertificates\Pages\ViewTrainingCertificate;
use Modules\Training\Filament\Resources\TrainingCertificates\Schemas\TrainingCertificateForm;
use Modules\Training\Filament\Resources\TrainingCertificates\Schemas\TrainingCertificateInfolist;
use Modules\Training\Filament\Resources\TrainingCertificates\Tables\TrainingCertificatesTable;
use Modules\Training\Models\TrainingCertificate;

class TrainingCertificateResource extends ModuleResource
{
    protected static ?string $model = TrainingCertificate::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return TrainingCertificateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainingCertificateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingCertificatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingCertificates::route('/'),
            'create' => CreateTrainingCertificate::route('/create'),
            'view' => ViewTrainingCertificate::route('/{record}'),
            'edit' => EditTrainingCertificate::route('/{record}/edit'),
        ];
    }
}
