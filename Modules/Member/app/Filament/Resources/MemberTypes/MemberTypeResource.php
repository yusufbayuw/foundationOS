<?php

namespace Modules\Member\Filament\Resources\MemberTypes;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Member\Filament\Resources\MemberTypes\Pages\CreateMemberType;
use Modules\Member\Filament\Resources\MemberTypes\Pages\EditMemberType;
use Modules\Member\Filament\Resources\MemberTypes\Pages\ListMemberTypes;
use Modules\Member\Filament\Resources\MemberTypes\Pages\ViewMemberType;
use Modules\Member\Filament\Resources\MemberTypes\Schemas\MemberTypeForm;
use Modules\Member\Filament\Resources\MemberTypes\Schemas\MemberTypeInfolist;
use Modules\Member\Filament\Resources\MemberTypes\Tables\MemberTypesTable;
use Modules\Member\Models\MemberType;

class MemberTypeResource extends ModuleResource
{
    protected static ?string $model = MemberType::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MemberTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MemberTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MemberTypesTable::configure($table);
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
            'index' => ListMemberTypes::route('/'),
            'create' => CreateMemberType::route('/create'),
            'view' => ViewMemberType::route('/{record}'),
            'edit' => EditMemberType::route('/{record}/edit'),
        ];
    }
}
