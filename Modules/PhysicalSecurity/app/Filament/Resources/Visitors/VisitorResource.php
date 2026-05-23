<?php

namespace Modules\PhysicalSecurity\Filament\Resources\Visitors;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\PhysicalSecurity\Filament\Resources\Visitors\Pages\CreateVisitor;
use Modules\PhysicalSecurity\Filament\Resources\Visitors\Pages\EditVisitor;
use Modules\PhysicalSecurity\Filament\Resources\Visitors\Pages\ListVisitors;
use Modules\PhysicalSecurity\Filament\Resources\Visitors\Pages\ViewVisitor;
use Modules\PhysicalSecurity\Filament\Resources\Visitors\Schemas\VisitorForm;
use Modules\PhysicalSecurity\Filament\Resources\Visitors\Tables\VisitorsTable;
use Modules\PhysicalSecurity\Models\Visitor;

class VisitorResource extends ModuleResource
{
    protected static ?string $model = Visitor::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VisitorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VisitorsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVisitors::route('/'),
            'create' => CreateVisitor::route('/create'),
            'view' => ViewVisitor::route('/{record}'),
            'edit' => EditVisitor::route('/{record}/edit'),
        ];
    }
}
