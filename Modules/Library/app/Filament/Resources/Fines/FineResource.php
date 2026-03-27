<?php

namespace Modules\Library\Filament\Resources\Fines;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Filament\Resources\Fines\Pages\CreateFine;
use Modules\Library\Filament\Resources\Fines\Pages\EditFine;
use Modules\Library\Filament\Resources\Fines\Pages\ListFines;
use Modules\Library\Filament\Resources\Fines\Pages\ViewFine;
use Modules\Library\Filament\Resources\Fines\Schemas\FineForm;
use Modules\Library\Filament\Resources\Fines\Schemas\FineInfolist;
use Modules\Library\Filament\Resources\Fines\Tables\FinesTable;
use Modules\Library\Models\Fine;

class FineResource extends LocalizedResource
{
    protected static ?string $model = Fine::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FineForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FineInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinesTable::configure($table);
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
            'index' => ListFines::route('/'),
            'create' => CreateFine::route('/create'),
            'view' => ViewFine::route('/{record}'),
            'edit' => EditFine::route('/{record}/edit'),
        ];
    }
}
