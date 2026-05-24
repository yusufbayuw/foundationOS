<?php

namespace Modules\Enrollment\Filament\Resources\PromoCodes;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Enrollment\Filament\Resources\PromoCodes\Pages\CreatePromoCode;
use Modules\Enrollment\Filament\Resources\PromoCodes\Pages\EditPromoCode;
use Modules\Enrollment\Filament\Resources\PromoCodes\Pages\ListPromoCodes;
use Modules\Enrollment\Filament\Resources\PromoCodes\Pages\ViewPromoCode;
use Modules\Enrollment\Filament\Resources\PromoCodes\Schemas\PromoCodeForm;
use Modules\Enrollment\Filament\Resources\PromoCodes\Schemas\PromoCodeInfolist;
use Modules\Enrollment\Filament\Resources\PromoCodes\Tables\PromoCodesTable;
use Modules\Enrollment\Models\PromoCode;

class PromoCodeResource extends ModuleResource
{
    protected static ?string $model = PromoCode::class;

    public static function form(Schema $schema): Schema
    {
        return PromoCodeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PromoCodeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromoCodesTable::configure($table);
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
            'index' => ListPromoCodes::route('/'),
            'create' => CreatePromoCode::route('/create'),
            'view' => ViewPromoCode::route('/{record}'),
            'edit' => EditPromoCode::route('/{record}/edit'),
        ];
    }
}
