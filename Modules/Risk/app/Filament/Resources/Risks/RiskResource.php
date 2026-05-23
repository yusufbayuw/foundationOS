<?php

namespace Modules\Risk\Filament\Resources\Risks;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Risk\Filament\Resources\Risks\Pages\CreateRisk;
use Modules\Risk\Filament\Resources\Risks\Pages\EditRisk;
use Modules\Risk\Filament\Resources\Risks\Pages\ListRisks;
use Modules\Risk\Filament\Resources\Risks\Pages\ViewRisk;
use Modules\Risk\Filament\Resources\Risks\Schemas\RiskForm;
use Modules\Risk\Filament\Resources\Risks\Tables\RisksTable;
use Modules\Risk\Models\Risk;

class RiskResource extends ModuleResource
{
    protected static ?string $model = Risk::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RiskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RisksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRisks::route('/'),
            'create' => CreateRisk::route('/create'),
            'view' => ViewRisk::route('/{record}'),
            'edit' => EditRisk::route('/{record}/edit'),
        ];
    }
}
