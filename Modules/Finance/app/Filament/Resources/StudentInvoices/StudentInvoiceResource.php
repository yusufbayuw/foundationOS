<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Concerns\ConfiguresGlobalSearch;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Finance\Filament\Resources\StudentInvoices\Pages\CreateStudentInvoice;
use Modules\Finance\Filament\Resources\StudentInvoices\Pages\EditStudentInvoice;
use Modules\Finance\Filament\Resources\StudentInvoices\Pages\ListStudentInvoices;
use Modules\Finance\Filament\Resources\StudentInvoices\Pages\ViewStudentInvoice;
use Modules\Finance\Filament\Resources\StudentInvoices\RelationManagers\ItemsRelationManager;
use Modules\Finance\Filament\Resources\StudentInvoices\RelationManagers\PaymentsRelationManager;
use Modules\Finance\Filament\Resources\StudentInvoices\Schemas\StudentInvoiceForm;
use Modules\Finance\Filament\Resources\StudentInvoices\Schemas\StudentInvoiceInfolist;
use Modules\Finance\Filament\Resources\StudentInvoices\Tables\StudentInvoicesTable;
use Modules\Finance\Models\StudentInvoice;

class StudentInvoiceResource extends LocalizedResource
{
    use ConfiguresGlobalSearch;

    protected static ?string $model = StudentInvoice::class;

    protected static ?string $recordTitleAttribute = 'invoice_number';

    /**
     * @return array<int, string>
     */
    protected static function globalSearchAttributes(): array
    {
        return ['invoice_number'];
    }

    /**
     * @return array<string, string>
     */
    protected static function globalSearchResultDetails(Model $record): array
    {
        assert($record instanceof StudentInvoice);

        return static::detailStatus($record->status);
    }

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

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
            ItemsRelationManager::class,
            PaymentsRelationManager::class,
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

    public static function canEdit(Model $record): bool
    {
        return $record instanceof StudentInvoice
            && ! $record->isLockedForMutation()
            && parent::canEdit($record);
    }
}
