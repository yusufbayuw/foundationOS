<?php

namespace Modules\School\Filament\Resources\StudentRiskScores;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\School\Filament\Resources\StudentRiskScores\Pages\CreateStudentRiskScore;
use Modules\School\Filament\Resources\StudentRiskScores\Pages\EditStudentRiskScore;
use Modules\School\Filament\Resources\StudentRiskScores\Pages\ListStudentRiskScores;
use Modules\School\Filament\Resources\StudentRiskScores\Pages\ViewStudentRiskScore;
use Modules\School\Filament\Resources\StudentRiskScores\Schemas\StudentRiskScoreForm;
use Modules\School\Filament\Resources\StudentRiskScores\Schemas\StudentRiskScoreInfolist;
use Modules\School\Filament\Resources\StudentRiskScores\Tables\StudentRiskScoresTable;
use Modules\School\Models\StudentRiskScore;

class StudentRiskScoreResource extends ModuleResource
{
    protected static ?string $model = StudentRiskScore::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return StudentRiskScoreForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentRiskScoreInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentRiskScoresTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentRiskScores::route('/'),
            'create' => CreateStudentRiskScore::route('/create'),
            'view' => ViewStudentRiskScore::route('/{record}'),
            'edit' => EditStudentRiskScore::route('/{record}/edit'),
        ];
    }
}
