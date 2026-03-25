<?php

namespace Modules\School\Filament\Resources\ViolationTypes;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\ViolationTypes\Pages\CreateViolationType;
use Modules\School\Filament\Resources\ViolationTypes\Pages\EditViolationType;
use Modules\School\Filament\Resources\ViolationTypes\Pages\ListViolationTypes;
use Modules\School\Filament\Resources\ViolationTypes\Pages\ViewViolationType;
use Modules\School\Filament\Resources\ViolationTypes\Schemas\ViolationTypeForm;
use Modules\School\Filament\Resources\ViolationTypes\Schemas\ViolationTypeInfolist;
use Modules\School\Filament\Resources\ViolationTypes\Tables\ViolationTypesTable;
use Modules\School\Models\ViolationType;

class ViolationTypeResource extends LocalizedResource
{
    protected static ?string $model = ViolationType::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ViolationTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ViolationTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ViolationTypesTable::configure($table);
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
            'index' => ListViolationTypes::route('/'),
            'create' => CreateViolationType::route('/create'),
            'view' => ViewViolationType::route('/{record}'),
            'edit' => EditViolationType::route('/{record}/edit'),
        ];
    }
}
