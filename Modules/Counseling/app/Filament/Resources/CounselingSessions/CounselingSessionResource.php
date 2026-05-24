<?php

namespace Modules\Counseling\Filament\Resources\CounselingSessions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Counseling\Filament\Resources\CounselingSessions\Pages\CreateCounselingSession;
use Modules\Counseling\Filament\Resources\CounselingSessions\Pages\EditCounselingSession;
use Modules\Counseling\Filament\Resources\CounselingSessions\Pages\ListCounselingSessions;
use Modules\Counseling\Filament\Resources\CounselingSessions\Pages\ViewCounselingSession;
use Modules\Counseling\Filament\Resources\CounselingSessions\Schemas\CounselingSessionForm;
use Modules\Counseling\Filament\Resources\CounselingSessions\Schemas\CounselingSessionInfolist;
use Modules\Counseling\Filament\Resources\CounselingSessions\Tables\CounselingSessionsTable;
use Modules\Counseling\Models\CounselingSession;

class CounselingSessionResource extends ModuleResource
{
    protected static ?string $model = CounselingSession::class;

    public static function form(Schema $schema): Schema
    {
        return CounselingSessionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CounselingSessionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CounselingSessionsTable::configure($table);
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
            'index' => ListCounselingSessions::route('/'),
            'create' => CreateCounselingSession::route('/create'),
            'view' => ViewCounselingSession::route('/{record}'),
            'edit' => EditCounselingSession::route('/{record}/edit'),
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
