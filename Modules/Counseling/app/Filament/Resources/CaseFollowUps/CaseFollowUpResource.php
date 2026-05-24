<?php

namespace Modules\Counseling\Filament\Resources\CaseFollowUps;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Counseling\Filament\Resources\CaseFollowUps\Pages\CreateCaseFollowUp;
use Modules\Counseling\Filament\Resources\CaseFollowUps\Pages\EditCaseFollowUp;
use Modules\Counseling\Filament\Resources\CaseFollowUps\Pages\ListCaseFollowUps;
use Modules\Counseling\Filament\Resources\CaseFollowUps\Pages\ViewCaseFollowUp;
use Modules\Counseling\Filament\Resources\CaseFollowUps\Schemas\CaseFollowUpForm;
use Modules\Counseling\Filament\Resources\CaseFollowUps\Schemas\CaseFollowUpInfolist;
use Modules\Counseling\Filament\Resources\CaseFollowUps\Tables\CaseFollowUpsTable;
use Modules\Counseling\Models\CaseFollowUp;

class CaseFollowUpResource extends ModuleResource
{
    protected static ?string $model = CaseFollowUp::class;

    public static function form(Schema $schema): Schema
    {
        return CaseFollowUpForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CaseFollowUpInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CaseFollowUpsTable::configure($table);
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
            'index' => ListCaseFollowUps::route('/'),
            'create' => CreateCaseFollowUp::route('/create'),
            'view' => ViewCaseFollowUp::route('/{record}'),
            'edit' => EditCaseFollowUp::route('/{record}/edit'),
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
