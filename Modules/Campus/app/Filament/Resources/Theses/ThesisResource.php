<?php

namespace Modules\Campus\Filament\Resources\Theses;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\Theses\Pages\CreateThesis;
use Modules\Campus\Filament\Resources\Theses\Pages\EditThesis;
use Modules\Campus\Filament\Resources\Theses\Pages\ListTheses;
use Modules\Campus\Filament\Resources\Theses\Pages\ViewThesis;
use Modules\Campus\Filament\Resources\Theses\Schemas\ThesisForm;
use Modules\Campus\Filament\Resources\Theses\Schemas\ThesisInfolist;
use Modules\Campus\Filament\Resources\Theses\Tables\ThesesTable;
use Modules\Campus\Models\Thesis;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;

class ThesisResource extends LocalizedResource
{
    protected static ?string $model = Thesis::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ThesisForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ThesisInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ThesesTable::configure($table);
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
            'index' => ListTheses::route('/'),
            'create' => CreateThesis::route('/create'),
            'view' => ViewThesis::route('/{record}'),
            'edit' => EditThesis::route('/{record}/edit'),
        ];
    }
}
