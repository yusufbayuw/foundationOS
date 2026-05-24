<?php

namespace Modules\Counseling\Filament\Resources\Counselors;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Counseling\Filament\Resources\Counselors\Pages\CreateCounselor;
use Modules\Counseling\Filament\Resources\Counselors\Pages\EditCounselor;
use Modules\Counseling\Filament\Resources\Counselors\Pages\ListCounselors;
use Modules\Counseling\Filament\Resources\Counselors\Pages\ViewCounselor;
use Modules\Counseling\Filament\Resources\Counselors\Schemas\CounselorForm;
use Modules\Counseling\Filament\Resources\Counselors\Schemas\CounselorInfolist;
use Modules\Counseling\Filament\Resources\Counselors\Tables\CounselorsTable;
use Modules\Counseling\Models\Counselor;

class CounselorResource extends ModuleResource
{
    protected static ?string $model = Counselor::class;

    public static function form(Schema $schema): Schema
    {
        return CounselorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CounselorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CounselorsTable::configure($table);
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
            'index' => ListCounselors::route('/'),
            'create' => CreateCounselor::route('/create'),
            'view' => ViewCounselor::route('/{record}'),
            'edit' => EditCounselor::route('/{record}/edit'),
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
