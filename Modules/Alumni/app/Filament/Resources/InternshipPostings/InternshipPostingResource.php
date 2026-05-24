<?php

namespace Modules\Alumni\Filament\Resources\InternshipPostings;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Alumni\Filament\Resources\InternshipPostings\Pages\CreateInternshipPosting;
use Modules\Alumni\Filament\Resources\InternshipPostings\Pages\EditInternshipPosting;
use Modules\Alumni\Filament\Resources\InternshipPostings\Pages\ListInternshipPostings;
use Modules\Alumni\Filament\Resources\InternshipPostings\Pages\ViewInternshipPosting;
use Modules\Alumni\Filament\Resources\InternshipPostings\Schemas\InternshipPostingForm;
use Modules\Alumni\Filament\Resources\InternshipPostings\Schemas\InternshipPostingInfolist;
use Modules\Alumni\Filament\Resources\InternshipPostings\Tables\InternshipPostingsTable;
use Modules\Alumni\Models\InternshipPosting;
use Modules\Core\Filament\Support\ModuleResource;

class InternshipPostingResource extends ModuleResource
{
    protected static ?string $model = InternshipPosting::class;

    public static function form(Schema $schema): Schema
    {
        return InternshipPostingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InternshipPostingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InternshipPostingsTable::configure($table);
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
            'index' => ListInternshipPostings::route('/'),
            'create' => CreateInternshipPosting::route('/create'),
            'view' => ViewInternshipPosting::route('/{record}'),
            'edit' => EditInternshipPosting::route('/{record}/edit'),
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
