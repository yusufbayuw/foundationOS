<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\StudentInvoices\Pages\CreateStudentInvoice;
use Modules\Finance\Filament\Resources\StudentInvoices\Pages\EditStudentInvoice;
use Modules\Finance\Filament\Resources\StudentInvoices\Pages\ListStudentInvoices;
use Modules\Finance\Filament\Resources\StudentInvoices\Pages\ViewStudentInvoice;
use Modules\Finance\Filament\Resources\StudentInvoices\Schemas\StudentInvoiceForm;
use Modules\Finance\Filament\Resources\StudentInvoices\Schemas\StudentInvoiceInfolist;
use Modules\Finance\Filament\Resources\StudentInvoices\Tables\StudentInvoicesTable;
use Modules\Finance\Models\StudentInvoice;

class StudentInvoiceResource extends LocalizedResource
{
    protected static ?string $model = StudentInvoice::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StudentInvoiceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentInvoiceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentInvoicesTable::configure($table);
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
            'index' => ListStudentInvoices::route('/'),
            'create' => CreateStudentInvoice::route('/create'),
            'view' => ViewStudentInvoice::route('/{record}'),
            'edit' => EditStudentInvoice::route('/{record}/edit'),
        ];
    }
}
