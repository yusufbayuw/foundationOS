<?php

namespace Modules\Boarding\Filament\Resources\Dormitories;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Boarding\Filament\Resources\Dormitories\Pages\CreateDormitory;
use Modules\Boarding\Filament\Resources\Dormitories\Pages\EditDormitory;
use Modules\Boarding\Filament\Resources\Dormitories\Pages\ListDormitories;
use Modules\Boarding\Filament\Resources\Dormitories\Pages\ViewDormitory;
use Modules\Boarding\Filament\Resources\Dormitories\Schemas\DormitoryForm;
use Modules\Boarding\Filament\Resources\Dormitories\Tables\DormitoriesTable;
use Modules\Boarding\Models\Dormitory;
use Modules\Core\Filament\Support\ModuleResource;

class DormitoryResource extends ModuleResource
{
    protected static ?string $model = Dormitory::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DormitoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DormitoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDormitories::route('/'),
            'create' => CreateDormitory::route('/create'),
            'view' => ViewDormitory::route('/{record}'),
            'edit' => EditDormitory::route('/{record}/edit'),
        ];
    }
}
