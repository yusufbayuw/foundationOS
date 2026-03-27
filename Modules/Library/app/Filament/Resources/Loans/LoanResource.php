<?php

namespace Modules\Library\Filament\Resources\Loans;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Filament\Resources\Loans\Pages\CreateLoan;
use Modules\Library\Filament\Resources\Loans\Pages\EditLoan;
use Modules\Library\Filament\Resources\Loans\Pages\ListLoans;
use Modules\Library\Filament\Resources\Loans\Pages\ViewLoan;
use Modules\Library\Filament\Resources\Loans\RelationManagers\FinesRelationManager;
use Modules\Library\Filament\Resources\Loans\Schemas\LoanForm;
use Modules\Library\Filament\Resources\Loans\Schemas\LoanInfolist;
use Modules\Library\Filament\Resources\Loans\Tables\LoansTable;
use Modules\Library\Models\Loan;

class LoanResource extends LocalizedResource
{
    protected static ?string $model = Loan::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LoanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LoanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            FinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoans::route('/'),
            'create' => CreateLoan::route('/create'),
            'view' => ViewLoan::route('/{record}'),
            'edit' => EditLoan::route('/{record}/edit'),
        ];
    }
}
