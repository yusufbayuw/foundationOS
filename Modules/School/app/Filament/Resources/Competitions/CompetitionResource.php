<?php

namespace Modules\School\Filament\Resources\Competitions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\School\Filament\Resources\Competitions\Pages\CreateCompetition;
use Modules\School\Filament\Resources\Competitions\Pages\EditCompetition;
use Modules\School\Filament\Resources\Competitions\Pages\ListCompetitions;
use Modules\School\Filament\Resources\Competitions\Pages\ViewCompetition;
use Modules\School\Filament\Resources\Competitions\Schemas\CompetitionForm;
use Modules\School\Filament\Resources\Competitions\Schemas\CompetitionInfolist;
use Modules\School\Filament\Resources\Competitions\Tables\CompetitionsTable;
use Modules\School\Models\Competition;

class CompetitionResource extends ModuleResource
{
    protected static ?string $model = Competition::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CompetitionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompetitionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompetitionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompetitions::route('/'),
            'create' => CreateCompetition::route('/create'),
            'view' => ViewCompetition::route('/{record}'),
            'edit' => EditCompetition::route('/{record}/edit'),
        ];
    }
}
