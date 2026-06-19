<?php

namespace Modules\Employee\Filament\Resources\KpiScores;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Employee\Filament\Resources\KpiScores\Pages\CreateKpiScore;
use Modules\Employee\Filament\Resources\KpiScores\Pages\EditKpiScore;
use Modules\Employee\Filament\Resources\KpiScores\Pages\ListKpiScores;
use Modules\Employee\Filament\Resources\KpiScores\Pages\ViewKpiScore;
use Modules\Employee\Filament\Resources\KpiScores\Schemas\KpiScoreForm;
use Modules\Employee\Filament\Resources\KpiScores\Schemas\KpiScoreInfolist;
use Modules\Employee\Filament\Resources\KpiScores\Tables\KpiScoresTable;
use Modules\Employee\Models\KpiScore;

class KpiScoreResource extends LocalizedResource
{
    protected static ?string $model = KpiScore::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return KpiScoreForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KpiScoreInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KpiScoresTable::configure($table);
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
            'index' => ListKpiScores::route('/'),
            'create' => CreateKpiScore::route('/create'),
            'view' => ViewKpiScore::route('/{record}'),
            'edit' => EditKpiScore::route('/{record}/edit'),
        ];
    }
}
