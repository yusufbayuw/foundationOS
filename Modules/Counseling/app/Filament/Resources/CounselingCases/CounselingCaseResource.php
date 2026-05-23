<?php

namespace Modules\Counseling\Filament\Resources\CounselingCases;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Counseling\Filament\Resources\CounselingCases\Pages\CreateCounselingCase;
use Modules\Counseling\Filament\Resources\CounselingCases\Pages\EditCounselingCase;
use Modules\Counseling\Filament\Resources\CounselingCases\Pages\ListCounselingCases;
use Modules\Counseling\Filament\Resources\CounselingCases\Pages\ViewCounselingCase;
use Modules\Counseling\Filament\Resources\CounselingCases\Schemas\CounselingCaseForm;
use Modules\Counseling\Filament\Resources\CounselingCases\Tables\CounselingCasesTable;
use Modules\Counseling\Models\CounselingCase;

class CounselingCaseResource extends ModuleResource
{
    protected static ?string $model = CounselingCase::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CounselingCaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CounselingCasesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCounselingCases::route('/'),
            'create' => CreateCounselingCase::route('/create'),
            'view' => ViewCounselingCase::route('/{record}'),
            'edit' => EditCounselingCase::route('/{record}/edit'),
        ];
    }
}
