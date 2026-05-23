<?php

namespace Modules\ItOps\Filament\Resources\SoftwareLicenses;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\ItOps\Filament\Resources\SoftwareLicenses\Pages\CreateSoftwareLicense;
use Modules\ItOps\Filament\Resources\SoftwareLicenses\Pages\EditSoftwareLicense;
use Modules\ItOps\Filament\Resources\SoftwareLicenses\Pages\ListSoftwareLicenses;
use Modules\ItOps\Filament\Resources\SoftwareLicenses\Pages\ViewSoftwareLicense;
use Modules\ItOps\Filament\Resources\SoftwareLicenses\Schemas\SoftwareLicenseForm;
use Modules\ItOps\Filament\Resources\SoftwareLicenses\Tables\SoftwareLicensesTable;
use Modules\ItOps\Models\SoftwareLicense;

class SoftwareLicenseResource extends ModuleResource
{
    protected static ?string $model = SoftwareLicense::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SoftwareLicenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SoftwareLicensesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSoftwareLicenses::route('/'),
            'create' => CreateSoftwareLicense::route('/create'),
            'view' => ViewSoftwareLicense::route('/{record}'),
            'edit' => EditSoftwareLicense::route('/{record}/edit'),
        ];
    }
}
