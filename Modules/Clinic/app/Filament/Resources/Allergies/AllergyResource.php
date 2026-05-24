<?php

namespace Modules\Clinic\Filament\Resources\Allergies;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Clinic\Filament\Resources\Allergies\Pages\CreateAllergy;
use Modules\Clinic\Filament\Resources\Allergies\Pages\EditAllergy;
use Modules\Clinic\Filament\Resources\Allergies\Pages\ListAllergies;
use Modules\Clinic\Filament\Resources\Allergies\Pages\ViewAllergy;
use Modules\Clinic\Filament\Resources\Allergies\Schemas\AllergyForm;
use Modules\Clinic\Filament\Resources\Allergies\Schemas\AllergyInfolist;
use Modules\Clinic\Filament\Resources\Allergies\Tables\AllergiesTable;
use Modules\Clinic\Models\Allergy;
use Modules\Core\Filament\Support\ModuleResource;

class AllergyResource extends ModuleResource
{
    protected static ?string $model = Allergy::class;

    public static function form(Schema $schema): Schema
    {
        return AllergyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AllergyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AllergiesTable::configure($table);
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
            'index' => ListAllergies::route('/'),
            'create' => CreateAllergy::route('/create'),
            'view' => ViewAllergy::route('/{record}'),
            'edit' => EditAllergy::route('/{record}/edit'),
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
