<?php

namespace Modules\EOffice\Filament\Resources\LetterDispositions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EOffice\Filament\Resources\LetterDispositions\Pages\CreateLetterDisposition;
use Modules\EOffice\Filament\Resources\LetterDispositions\Pages\EditLetterDisposition;
use Modules\EOffice\Filament\Resources\LetterDispositions\Pages\ListLetterDispositions;
use Modules\EOffice\Filament\Resources\LetterDispositions\Pages\ViewLetterDisposition;
use Modules\EOffice\Filament\Resources\LetterDispositions\Schemas\LetterDispositionForm;
use Modules\EOffice\Filament\Resources\LetterDispositions\Schemas\LetterDispositionInfolist;
use Modules\EOffice\Filament\Resources\LetterDispositions\Tables\LetterDispositionsTable;
use Modules\EOffice\Models\LetterDisposition;

class LetterDispositionResource extends ModuleResource
{
    protected static ?string $model = LetterDisposition::class;

    public static function form(Schema $schema): Schema
    {
        return LetterDispositionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LetterDispositionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LetterDispositionsTable::configure($table);
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
            'index' => ListLetterDispositions::route('/'),
            'create' => CreateLetterDisposition::route('/create'),
            'view' => ViewLetterDisposition::route('/{record}'),
            'edit' => EditLetterDisposition::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
