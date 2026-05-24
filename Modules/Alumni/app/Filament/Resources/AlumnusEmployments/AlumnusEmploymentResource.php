<?php

namespace Modules\Alumni\Filament\Resources\AlumnusEmployments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Alumni\Filament\Resources\AlumnusEmployments\Pages\CreateAlumnusEmployment;
use Modules\Alumni\Filament\Resources\AlumnusEmployments\Pages\EditAlumnusEmployment;
use Modules\Alumni\Filament\Resources\AlumnusEmployments\Pages\ListAlumnusEmployments;
use Modules\Alumni\Filament\Resources\AlumnusEmployments\Pages\ViewAlumnusEmployment;
use Modules\Alumni\Filament\Resources\AlumnusEmployments\Schemas\AlumnusEmploymentForm;
use Modules\Alumni\Filament\Resources\AlumnusEmployments\Schemas\AlumnusEmploymentInfolist;
use Modules\Alumni\Filament\Resources\AlumnusEmployments\Tables\AlumnusEmploymentsTable;
use Modules\Alumni\Models\AlumnusEmployment;
use Modules\Core\Filament\Support\ModuleResource;

class AlumnusEmploymentResource extends ModuleResource
{
    protected static ?string $model = AlumnusEmployment::class;

    public static function form(Schema $schema): Schema
    {
        return AlumnusEmploymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AlumnusEmploymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlumnusEmploymentsTable::configure($table);
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
            'index' => ListAlumnusEmployments::route('/'),
            'create' => CreateAlumnusEmployment::route('/create'),
            'view' => ViewAlumnusEmployment::route('/{record}'),
            'edit' => EditAlumnusEmployment::route('/{record}/edit'),
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
