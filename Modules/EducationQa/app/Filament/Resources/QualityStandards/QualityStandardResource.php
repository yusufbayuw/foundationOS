<?php

namespace Modules\EducationQa\Filament\Resources\QualityStandards;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EducationQa\Filament\Resources\QualityStandards\Pages\CreateQualityStandard;
use Modules\EducationQa\Filament\Resources\QualityStandards\Pages\EditQualityStandard;
use Modules\EducationQa\Filament\Resources\QualityStandards\Pages\ListQualityStandards;
use Modules\EducationQa\Filament\Resources\QualityStandards\Pages\ViewQualityStandard;
use Modules\EducationQa\Filament\Resources\QualityStandards\Schemas\QualityStandardForm;
use Modules\EducationQa\Filament\Resources\QualityStandards\Tables\QualityStandardsTable;
use Modules\EducationQa\Models\QualityStandard;

class QualityStandardResource extends ModuleResource
{
    protected static ?string $model = QualityStandard::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return QualityStandardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QualityStandardsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQualityStandards::route('/'),
            'create' => CreateQualityStandard::route('/create'),
            'view' => ViewQualityStandard::route('/{record}'),
            'edit' => EditQualityStandard::route('/{record}/edit'),
        ];
    }
}
