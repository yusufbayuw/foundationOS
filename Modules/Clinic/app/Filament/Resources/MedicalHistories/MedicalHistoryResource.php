<?php

namespace Modules\Clinic\Filament\Resources\MedicalHistories;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Clinic\Filament\Resources\MedicalHistories\Pages\CreateMedicalHistory;
use Modules\Clinic\Filament\Resources\MedicalHistories\Pages\EditMedicalHistory;
use Modules\Clinic\Filament\Resources\MedicalHistories\Pages\ListMedicalHistories;
use Modules\Clinic\Filament\Resources\MedicalHistories\Pages\ViewMedicalHistory;
use Modules\Clinic\Filament\Resources\MedicalHistories\Schemas\MedicalHistoryForm;
use Modules\Clinic\Filament\Resources\MedicalHistories\Schemas\MedicalHistoryInfolist;
use Modules\Clinic\Filament\Resources\MedicalHistories\Tables\MedicalHistoriesTable;
use Modules\Clinic\Models\MedicalHistory;
use Modules\Core\Filament\Support\ModuleResource;

class MedicalHistoryResource extends ModuleResource
{
    protected static ?string $model = MedicalHistory::class;

    public static function form(Schema $schema): Schema
    {
        return MedicalHistoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MedicalHistoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicalHistoriesTable::configure($table);
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
            'index' => ListMedicalHistories::route('/'),
            'create' => CreateMedicalHistory::route('/create'),
            'view' => ViewMedicalHistory::route('/{record}'),
            'edit' => EditMedicalHistory::route('/{record}/edit'),
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
