<?php

namespace Modules\Consulting\Filament\Resources\EngagementProposals;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Consulting\Filament\Resources\EngagementProposals\Pages\CreateEngagementProposal;
use Modules\Consulting\Filament\Resources\EngagementProposals\Pages\EditEngagementProposal;
use Modules\Consulting\Filament\Resources\EngagementProposals\Pages\ListEngagementProposals;
use Modules\Consulting\Filament\Resources\EngagementProposals\Pages\ViewEngagementProposal;
use Modules\Consulting\Filament\Resources\EngagementProposals\Schemas\EngagementProposalForm;
use Modules\Consulting\Filament\Resources\EngagementProposals\Schemas\EngagementProposalInfolist;
use Modules\Consulting\Filament\Resources\EngagementProposals\Tables\EngagementProposalsTable;
use Modules\Consulting\Models\EngagementProposal;
use Modules\Core\Filament\Support\ModuleResource;

class EngagementProposalResource extends ModuleResource
{
    protected static ?string $model = EngagementProposal::class;

    public static function form(Schema $schema): Schema
    {
        return EngagementProposalForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EngagementProposalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EngagementProposalsTable::configure($table);
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
            'index' => ListEngagementProposals::route('/'),
            'create' => CreateEngagementProposal::route('/create'),
            'view' => ViewEngagementProposal::route('/{record}'),
            'edit' => EditEngagementProposal::route('/{record}/edit'),
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
