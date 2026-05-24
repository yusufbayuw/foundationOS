<?php

namespace Modules\EducationQa\Filament\Resources\AccreditationCycles;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EducationQa\Filament\Resources\AccreditationCycles\Pages\CreateAccreditationCycle;
use Modules\EducationQa\Filament\Resources\AccreditationCycles\Pages\EditAccreditationCycle;
use Modules\EducationQa\Filament\Resources\AccreditationCycles\Pages\ListAccreditationCycles;
use Modules\EducationQa\Filament\Resources\AccreditationCycles\Pages\ViewAccreditationCycle;
use Modules\EducationQa\Filament\Resources\AccreditationCycles\Schemas\AccreditationCycleForm;
use Modules\EducationQa\Filament\Resources\AccreditationCycles\Schemas\AccreditationCycleInfolist;
use Modules\EducationQa\Filament\Resources\AccreditationCycles\Tables\AccreditationCyclesTable;
use Modules\EducationQa\Models\AccreditationCycle;

class AccreditationCycleResource extends ModuleResource
{
    protected static ?string $model = AccreditationCycle::class;

    public static function form(Schema $schema): Schema
    {
        return AccreditationCycleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AccreditationCycleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccreditationCyclesTable::configure($table);
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
            'index' => ListAccreditationCycles::route('/'),
            'create' => CreateAccreditationCycle::route('/create'),
            'view' => ViewAccreditationCycle::route('/{record}'),
            'edit' => EditAccreditationCycle::route('/{record}/edit'),
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
