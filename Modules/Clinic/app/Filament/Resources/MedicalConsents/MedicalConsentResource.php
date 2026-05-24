<?php

namespace Modules\Clinic\Filament\Resources\MedicalConsents;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Clinic\Filament\Resources\MedicalConsents\Pages\CreateMedicalConsent;
use Modules\Clinic\Filament\Resources\MedicalConsents\Pages\EditMedicalConsent;
use Modules\Clinic\Filament\Resources\MedicalConsents\Pages\ListMedicalConsents;
use Modules\Clinic\Filament\Resources\MedicalConsents\Pages\ViewMedicalConsent;
use Modules\Clinic\Filament\Resources\MedicalConsents\Schemas\MedicalConsentForm;
use Modules\Clinic\Filament\Resources\MedicalConsents\Schemas\MedicalConsentInfolist;
use Modules\Clinic\Filament\Resources\MedicalConsents\Tables\MedicalConsentsTable;
use Modules\Clinic\Models\MedicalConsent;
use Modules\Core\Filament\Support\ModuleResource;

class MedicalConsentResource extends ModuleResource
{
    protected static ?string $model = MedicalConsent::class;

    public static function form(Schema $schema): Schema
    {
        return MedicalConsentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MedicalConsentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicalConsentsTable::configure($table);
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
            'index' => ListMedicalConsents::route('/'),
            'create' => CreateMedicalConsent::route('/create'),
            'view' => ViewMedicalConsent::route('/{record}'),
            'edit' => EditMedicalConsent::route('/{record}/edit'),
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
