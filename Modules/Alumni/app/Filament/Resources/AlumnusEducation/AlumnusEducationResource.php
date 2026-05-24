<?php

namespace Modules\Alumni\Filament\Resources\AlumnusEducation;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Alumni\Filament\Resources\AlumnusEducation\Pages\CreateAlumnusEducation;
use Modules\Alumni\Filament\Resources\AlumnusEducation\Pages\EditAlumnusEducation;
use Modules\Alumni\Filament\Resources\AlumnusEducation\Pages\ListAlumnusEducation;
use Modules\Alumni\Filament\Resources\AlumnusEducation\Pages\ViewAlumnusEducation;
use Modules\Alumni\Filament\Resources\AlumnusEducation\Schemas\AlumnusEducationForm;
use Modules\Alumni\Filament\Resources\AlumnusEducation\Schemas\AlumnusEducationInfolist;
use Modules\Alumni\Filament\Resources\AlumnusEducation\Tables\AlumnusEducationTable;
use Modules\Alumni\Models\AlumnusEducation;
use Modules\Core\Filament\Support\ModuleResource;

class AlumnusEducationResource extends ModuleResource
{
    protected static ?string $model = AlumnusEducation::class;

    public static function form(Schema $schema): Schema
    {
        return AlumnusEducationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AlumnusEducationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlumnusEducationTable::configure($table);
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
            'index' => ListAlumnusEducation::route('/'),
            'create' => CreateAlumnusEducation::route('/create'),
            'view' => ViewAlumnusEducation::route('/{record}'),
            'edit' => EditAlumnusEducation::route('/{record}/edit'),
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
