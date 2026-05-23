<?php

namespace Modules\Alumni\Filament\Resources\Alumni;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Alumni\Filament\Resources\Alumni\Pages\CreateAlumnus;
use Modules\Alumni\Filament\Resources\Alumni\Pages\EditAlumnus;
use Modules\Alumni\Filament\Resources\Alumni\Pages\ListAlumni;
use Modules\Alumni\Filament\Resources\Alumni\Pages\ViewAlumnus;
use Modules\Alumni\Filament\Resources\Alumni\Schemas\AlumnusForm;
use Modules\Alumni\Filament\Resources\Alumni\Tables\AlumniTable;
use Modules\Alumni\Models\Alumnus;
use Modules\Core\Filament\Support\ModuleResource;

class AlumnusResource extends ModuleResource
{
    protected static ?string $model = Alumnus::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AlumnusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlumniTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlumni::route('/'),
            'create' => CreateAlumnus::route('/create'),
            'view' => ViewAlumnus::route('/{record}'),
            'edit' => EditAlumnus::route('/{record}/edit'),
        ];
    }
}
