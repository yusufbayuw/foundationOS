<?php

namespace Modules\Core\Filament\Resources\AcademicPeriods;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\AcademicPeriods\Pages\CreateAcademicPeriod;
use Modules\Core\Filament\Resources\AcademicPeriods\Pages\EditAcademicPeriod;
use Modules\Core\Filament\Resources\AcademicPeriods\Pages\ListAcademicPeriods;
use Modules\Core\Filament\Resources\AcademicPeriods\Pages\ViewAcademicPeriod;
use Modules\Core\Filament\Resources\AcademicPeriods\Schemas\AcademicPeriodForm;
use Modules\Core\Filament\Resources\AcademicPeriods\Schemas\AcademicPeriodInfolist;
use Modules\Core\Filament\Resources\AcademicPeriods\Tables\AcademicPeriodsTable;
use Modules\Core\Models\AcademicPeriod;

class AcademicPeriodResource extends LocalizedResource
{
    protected static ?string $model = AcademicPeriod::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AcademicPeriodForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AcademicPeriodInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicPeriodsTable::configure($table);
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
            'index' => ListAcademicPeriods::route('/'),
            'create' => CreateAcademicPeriod::route('/create'),
            'view' => ViewAcademicPeriod::route('/{record}'),
            'edit' => EditAcademicPeriod::route('/{record}/edit'),
        ];
    }
}
