<?php

namespace Modules\Printing\Filament\Resources\PrintTemplates;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Printing\Filament\Resources\PrintTemplates\Pages\CreatePrintTemplate;
use Modules\Printing\Filament\Resources\PrintTemplates\Pages\EditPrintTemplate;
use Modules\Printing\Filament\Resources\PrintTemplates\Pages\ListPrintTemplates;
use Modules\Printing\Filament\Resources\PrintTemplates\Pages\ViewPrintTemplate;
use Modules\Printing\Filament\Resources\PrintTemplates\Schemas\PrintTemplateForm;
use Modules\Printing\Filament\Resources\PrintTemplates\Schemas\PrintTemplateInfolist;
use Modules\Printing\Filament\Resources\PrintTemplates\Tables\PrintTemplatesTable;
use Modules\Printing\Models\PrintTemplate;

class PrintTemplateResource extends ModuleResource
{
    protected static ?string $model = PrintTemplate::class;

    public static function form(Schema $schema): Schema
    {
        return PrintTemplateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrintTemplateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrintTemplatesTable::configure($table);
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
            'index' => ListPrintTemplates::route('/'),
            'create' => CreatePrintTemplate::route('/create'),
            'view' => ViewPrintTemplate::route('/{record}'),
            'edit' => EditPrintTemplate::route('/{record}/edit'),
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
