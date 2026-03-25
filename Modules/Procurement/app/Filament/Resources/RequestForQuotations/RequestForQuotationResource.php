<?php

namespace Modules\Procurement\Filament\Resources\RequestForQuotations;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Procurement\Filament\Resources\RequestForQuotations\Pages\CreateRequestForQuotation;
use Modules\Procurement\Filament\Resources\RequestForQuotations\Pages\EditRequestForQuotation;
use Modules\Procurement\Filament\Resources\RequestForQuotations\Pages\ListRequestForQuotations;
use Modules\Procurement\Filament\Resources\RequestForQuotations\Pages\ViewRequestForQuotation;
use Modules\Procurement\Filament\Resources\RequestForQuotations\Schemas\RequestForQuotationForm;
use Modules\Procurement\Filament\Resources\RequestForQuotations\Schemas\RequestForQuotationInfolist;
use Modules\Procurement\Filament\Resources\RequestForQuotations\Tables\RequestForQuotationsTable;
use Modules\Procurement\Models\RequestForQuotation;

class RequestForQuotationResource extends LocalizedResource
{
    protected static ?string $model = RequestForQuotation::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RequestForQuotationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RequestForQuotationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RequestForQuotationsTable::configure($table);
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
            'index' => ListRequestForQuotations::route('/'),
            'create' => CreateRequestForQuotation::route('/create'),
            'view' => ViewRequestForQuotation::route('/{record}'),
            'edit' => EditRequestForQuotation::route('/{record}/edit'),
        ];
    }
}
