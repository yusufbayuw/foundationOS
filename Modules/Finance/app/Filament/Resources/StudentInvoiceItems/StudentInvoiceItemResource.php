<?php

namespace Modules\Finance\Filament\Resources\StudentInvoiceItems;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\StudentInvoiceItems\Pages\CreateStudentInvoiceItem;
use Modules\Finance\Filament\Resources\StudentInvoiceItems\Pages\EditStudentInvoiceItem;
use Modules\Finance\Filament\Resources\StudentInvoiceItems\Pages\ListStudentInvoiceItems;
use Modules\Finance\Filament\Resources\StudentInvoiceItems\Pages\ViewStudentInvoiceItem;
use Modules\Finance\Filament\Resources\StudentInvoiceItems\Schemas\StudentInvoiceItemForm;
use Modules\Finance\Filament\Resources\StudentInvoiceItems\Schemas\StudentInvoiceItemInfolist;
use Modules\Finance\Filament\Resources\StudentInvoiceItems\Tables\StudentInvoiceItemsTable;
use Modules\Finance\Models\StudentInvoiceItem;

class StudentInvoiceItemResource extends LocalizedResource
{
    protected static ?string $model = StudentInvoiceItem::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StudentInvoiceItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentInvoiceItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentInvoiceItemsTable::configure($table);
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
            'index' => ListStudentInvoiceItems::route('/'),
            'create' => CreateStudentInvoiceItem::route('/create'),
            'view' => ViewStudentInvoiceItem::route('/{record}'),
            'edit' => EditStudentInvoiceItem::route('/{record}/edit'),
        ];
    }
}
