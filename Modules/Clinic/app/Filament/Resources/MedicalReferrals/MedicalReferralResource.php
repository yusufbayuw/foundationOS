<?php

namespace Modules\Clinic\Filament\Resources\MedicalReferrals;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Clinic\Filament\Resources\MedicalReferrals\Pages\CreateMedicalReferral;
use Modules\Clinic\Filament\Resources\MedicalReferrals\Pages\EditMedicalReferral;
use Modules\Clinic\Filament\Resources\MedicalReferrals\Pages\ListMedicalReferrals;
use Modules\Clinic\Filament\Resources\MedicalReferrals\Pages\ViewMedicalReferral;
use Modules\Clinic\Filament\Resources\MedicalReferrals\Schemas\MedicalReferralForm;
use Modules\Clinic\Filament\Resources\MedicalReferrals\Schemas\MedicalReferralInfolist;
use Modules\Clinic\Filament\Resources\MedicalReferrals\Tables\MedicalReferralsTable;
use Modules\Clinic\Models\MedicalReferral;
use Modules\Core\Filament\Support\ModuleResource;

class MedicalReferralResource extends ModuleResource
{
    protected static ?string $model = MedicalReferral::class;

    public static function form(Schema $schema): Schema
    {
        return MedicalReferralForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MedicalReferralInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicalReferralsTable::configure($table);
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
            'index' => ListMedicalReferrals::route('/'),
            'create' => CreateMedicalReferral::route('/create'),
            'view' => ViewMedicalReferral::route('/{record}'),
            'edit' => EditMedicalReferral::route('/{record}/edit'),
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
