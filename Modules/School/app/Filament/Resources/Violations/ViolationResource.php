<?php

namespace Modules\School\Filament\Resources\Violations;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\School\Filament\Resources\Violations\Pages\CreateViolation;
use Modules\School\Filament\Resources\Violations\Pages\EditViolation;
use Modules\School\Filament\Resources\Violations\Pages\ListViolations;
use Modules\School\Filament\Resources\Violations\Pages\ViewViolation;
use Modules\School\Filament\Resources\Violations\Schemas\ViolationForm;
use Modules\School\Filament\Resources\Violations\Schemas\ViolationInfolist;
use Modules\School\Filament\Resources\Violations\Tables\ViolationsTable;
use Modules\School\Models\Violation;

class ViolationResource extends LocalizedResource
{
    protected static ?string $model = Violation::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ViolationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ViolationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ViolationsTable::configure($table);
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
            'index' => ListViolations::route('/'),
            'create' => CreateViolation::route('/create'),
            'view' => ViewViolation::route('/{record}'),
            'edit' => EditViolation::route('/{record}/edit'),
        ];
    }
}
