<?php

namespace Modules\Consulting\Filament\Resources\ConsultingEngagements;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Consulting\Filament\Resources\ConsultingEngagements\Pages\CreateConsultingEngagement;
use Modules\Consulting\Filament\Resources\ConsultingEngagements\Pages\EditConsultingEngagement;
use Modules\Consulting\Filament\Resources\ConsultingEngagements\Pages\ListConsultingEngagements;
use Modules\Consulting\Filament\Resources\ConsultingEngagements\Pages\ViewConsultingEngagement;
use Modules\Consulting\Filament\Resources\ConsultingEngagements\Schemas\ConsultingEngagementForm;
use Modules\Consulting\Filament\Resources\ConsultingEngagements\Schemas\ConsultingEngagementInfolist;
use Modules\Consulting\Filament\Resources\ConsultingEngagements\Tables\ConsultingEngagementsTable;
use Modules\Consulting\Models\ConsultingEngagement;
use Modules\Core\Filament\Support\ModuleResource;

class ConsultingEngagementResource extends ModuleResource
{
    protected static ?string $model = ConsultingEngagement::class;

    public static function form(Schema $schema): Schema
    {
        return ConsultingEngagementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ConsultingEngagementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsultingEngagementsTable::configure($table);
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
            'index' => ListConsultingEngagements::route('/'),
            'create' => CreateConsultingEngagement::route('/create'),
            'view' => ViewConsultingEngagement::route('/{record}'),
            'edit' => EditConsultingEngagement::route('/{record}/edit'),
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
