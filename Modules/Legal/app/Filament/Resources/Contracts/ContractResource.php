<?php

namespace Modules\Legal\Filament\Resources\Contracts;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Legal\Filament\Resources\Contracts\Pages\CreateContract;
use Modules\Legal\Filament\Resources\Contracts\Pages\EditContract;
use Modules\Legal\Filament\Resources\Contracts\Pages\ListContracts;
use Modules\Legal\Filament\Resources\Contracts\Pages\ViewContract;
use Modules\Legal\Filament\Resources\Contracts\Schemas\ContractForm;
use Modules\Legal\Filament\Resources\Contracts\Tables\ContractsTable;
use Modules\Legal\Models\Contract;

class ContractResource extends ModuleResource
{
    protected static ?string $model = Contract::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ContractForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContractsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContracts::route('/'),
            'create' => CreateContract::route('/create'),
            'view' => ViewContract::route('/{record}'),
            'edit' => EditContract::route('/{record}/edit'),
        ];
    }
}
