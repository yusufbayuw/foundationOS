<?php

namespace Modules\School\Filament\Resources\Extracurriculars;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\School\Filament\Resources\Extracurriculars\Pages\CreateExtracurricular;
use Modules\School\Filament\Resources\Extracurriculars\Pages\EditExtracurricular;
use Modules\School\Filament\Resources\Extracurriculars\Pages\ListExtracurriculars;
use Modules\School\Filament\Resources\Extracurriculars\Schemas\ExtracurricularForm;
use Modules\School\Filament\Resources\Extracurriculars\Tables\ExtracurricularsTable;
use Modules\School\Models\Extracurricular;

class ExtracurricularResource extends ModuleResource
{
    protected static ?string $model = Extracurricular::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ExtracurricularForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExtracurricularsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExtracurriculars::route('/'),
            'create' => CreateExtracurricular::route('/create'),
            'edit' => EditExtracurricular::route('/{record}/edit'),
        ];
    }
}
