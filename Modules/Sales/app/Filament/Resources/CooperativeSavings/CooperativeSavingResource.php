<?php

namespace Modules\Sales\Filament\Resources\CooperativeSavings;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Sales\Filament\Resources\CooperativeSavings\Pages\CreateCooperativeSaving;
use Modules\Sales\Filament\Resources\CooperativeSavings\Pages\EditCooperativeSaving;
use Modules\Sales\Filament\Resources\CooperativeSavings\Pages\ListCooperativeSavings;
use Modules\Sales\Filament\Resources\CooperativeSavings\Pages\ViewCooperativeSaving;
use Modules\Sales\Filament\Resources\CooperativeSavings\Schemas\CooperativeSavingForm;
use Modules\Sales\Filament\Resources\CooperativeSavings\Schemas\CooperativeSavingInfolist;
use Modules\Sales\Filament\Resources\CooperativeSavings\Tables\CooperativeSavingsTable;
use Modules\Sales\Models\CooperativeSaving;

class CooperativeSavingResource extends ModuleResource
{
    protected static ?string $model = CooperativeSaving::class;

    protected static ?string $recordTitleAttribute = 'status';

    public static function form(Schema $schema): Schema
    {
        return CooperativeSavingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CooperativeSavingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CooperativeSavingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCooperativeSavings::route('/'),
            'create' => CreateCooperativeSaving::route('/create'),
            'view' => ViewCooperativeSaving::route('/{record}'),
            'edit' => EditCooperativeSaving::route('/{record}/edit'),
        ];
    }
}
