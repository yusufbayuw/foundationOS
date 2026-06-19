<?php

namespace Modules\Finance\Filament\Resources\TuitionTypes;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Finance\Filament\Resources\TuitionTypes\Pages\CreateTuitionType;
use Modules\Finance\Filament\Resources\TuitionTypes\Pages\EditTuitionType;
use Modules\Finance\Filament\Resources\TuitionTypes\Pages\ListTuitionTypes;
use Modules\Finance\Filament\Resources\TuitionTypes\Pages\ViewTuitionType;
use Modules\Finance\Filament\Resources\TuitionTypes\Schemas\TuitionTypeForm;
use Modules\Finance\Filament\Resources\TuitionTypes\Schemas\TuitionTypeInfolist;
use Modules\Finance\Filament\Resources\TuitionTypes\Tables\TuitionTypesTable;
use Modules\Finance\Models\TuitionType;

class TuitionTypeResource extends LocalizedResource
{
    protected static ?string $model = TuitionType::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TuitionTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TuitionTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TuitionTypesTable::configure($table);
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
            'index' => ListTuitionTypes::route('/'),
            'create' => CreateTuitionType::route('/create'),
            'view' => ViewTuitionType::route('/{record}'),
            'edit' => EditTuitionType::route('/{record}/edit'),
        ];
    }
}
