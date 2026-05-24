<?php

namespace Modules\Core\Filament\Resources\Stakeholders;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Resources\Stakeholders\Pages\CreateStakeholder;
use Modules\Core\Filament\Resources\Stakeholders\Pages\EditStakeholder;
use Modules\Core\Filament\Resources\Stakeholders\Pages\ListStakeholders;
use Modules\Core\Filament\Resources\Stakeholders\Pages\ViewStakeholder;
use Modules\Core\Filament\Resources\Stakeholders\Schemas\StakeholderForm;
use Modules\Core\Filament\Resources\Stakeholders\Schemas\StakeholderInfolist;
use Modules\Core\Filament\Resources\Stakeholders\Tables\StakeholdersTable;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Core\Models\Stakeholder;

class StakeholderResource extends ModuleResource
{
    protected static ?string $model = Stakeholder::class;

    public static function form(Schema $schema): Schema
    {
        return StakeholderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StakeholderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StakeholdersTable::configure($table);
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
            'index' => ListStakeholders::route('/'),
            'create' => CreateStakeholder::route('/create'),
            'view' => ViewStakeholder::route('/{record}'),
            'edit' => EditStakeholder::route('/{record}/edit'),
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
