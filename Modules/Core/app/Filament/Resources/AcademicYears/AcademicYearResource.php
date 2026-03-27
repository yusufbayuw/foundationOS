<?php

namespace Modules\Core\Filament\Resources\AcademicYears;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\AcademicYears\Pages\CreateAcademicYear;
use Modules\Core\Filament\Resources\AcademicYears\Pages\EditAcademicYear;
use Modules\Core\Filament\Resources\AcademicYears\Pages\ListAcademicYears;
use Modules\Core\Filament\Resources\AcademicYears\Pages\ViewAcademicYear;
use Modules\Core\Filament\Resources\AcademicYears\RelationManagers\AcademicPeriodsRelationManager;
use Modules\Core\Filament\Resources\AcademicYears\Schemas\AcademicYearForm;
use Modules\Core\Filament\Resources\AcademicYears\Schemas\AcademicYearInfolist;
use Modules\Core\Filament\Resources\AcademicYears\Tables\AcademicYearsTable;
use Modules\Core\Models\AcademicYear;

class AcademicYearResource extends LocalizedResource
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AcademicYearForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AcademicYearInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicYearsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AcademicPeriodsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAcademicYears::route('/'),
            'create' => CreateAcademicYear::route('/create'),
            'view' => ViewAcademicYear::route('/{record}'),
            'edit' => EditAcademicYear::route('/{record}/edit'),
        ];
    }
}
