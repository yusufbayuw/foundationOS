<?php

namespace Modules\Counseling\Filament\Resources\CounselingNotes;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Counseling\Filament\Resources\CounselingNotes\Pages\CreateCounselingNote;
use Modules\Counseling\Filament\Resources\CounselingNotes\Pages\EditCounselingNote;
use Modules\Counseling\Filament\Resources\CounselingNotes\Pages\ListCounselingNotes;
use Modules\Counseling\Filament\Resources\CounselingNotes\Pages\ViewCounselingNote;
use Modules\Counseling\Filament\Resources\CounselingNotes\Schemas\CounselingNoteForm;
use Modules\Counseling\Filament\Resources\CounselingNotes\Schemas\CounselingNoteInfolist;
use Modules\Counseling\Filament\Resources\CounselingNotes\Tables\CounselingNotesTable;
use Modules\Counseling\Models\CounselingNote;

class CounselingNoteResource extends ModuleResource
{
    protected static ?string $model = CounselingNote::class;

    public static function form(Schema $schema): Schema
    {
        return CounselingNoteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CounselingNoteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CounselingNotesTable::configure($table);
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
            'index' => ListCounselingNotes::route('/'),
            'create' => CreateCounselingNote::route('/create'),
            'view' => ViewCounselingNote::route('/{record}'),
            'edit' => EditCounselingNote::route('/{record}/edit'),
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
