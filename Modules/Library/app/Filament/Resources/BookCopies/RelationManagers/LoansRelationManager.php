<?php

namespace Modules\Library\Filament\Resources\BookCopies\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\Loans\Schemas\LoanForm;
use Modules\Library\Filament\Resources\Loans\Tables\LoansTable;

class LoansRelationManager extends RelationManager
{
    protected static string $relationship = 'loans';

    public function form(Schema $schema): Schema
    {
        return LoanForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return LoansTable::configure($table);
    }
}
