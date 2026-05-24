<?php

namespace Modules\Counseling\Filament\Resources\AnonymousReports;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Counseling\Filament\Resources\AnonymousReports\Pages\CreateAnonymousReport;
use Modules\Counseling\Filament\Resources\AnonymousReports\Pages\EditAnonymousReport;
use Modules\Counseling\Filament\Resources\AnonymousReports\Pages\ListAnonymousReports;
use Modules\Counseling\Filament\Resources\AnonymousReports\Pages\ViewAnonymousReport;
use Modules\Counseling\Filament\Resources\AnonymousReports\Schemas\AnonymousReportForm;
use Modules\Counseling\Filament\Resources\AnonymousReports\Schemas\AnonymousReportInfolist;
use Modules\Counseling\Filament\Resources\AnonymousReports\Tables\AnonymousReportsTable;
use Modules\Counseling\Models\AnonymousReport;

class AnonymousReportResource extends ModuleResource
{
    protected static ?string $model = AnonymousReport::class;

    public static function form(Schema $schema): Schema
    {
        return AnonymousReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AnonymousReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnonymousReportsTable::configure($table);
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
            'index' => ListAnonymousReports::route('/'),
            'create' => CreateAnonymousReport::route('/create'),
            'view' => ViewAnonymousReport::route('/{record}'),
            'edit' => EditAnonymousReport::route('/{record}/edit'),
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
