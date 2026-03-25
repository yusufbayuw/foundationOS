<?php

namespace Modules\Library\Filament\Resources\Members;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\Members\Pages\CreateMember;
use Modules\Library\Filament\Resources\Members\Pages\EditMember;
use Modules\Library\Filament\Resources\Members\Pages\ListMembers;
use Modules\Library\Filament\Resources\Members\Pages\ViewMember;
use Modules\Library\Filament\Resources\Members\Schemas\MemberForm;
use Modules\Library\Filament\Resources\Members\Schemas\MemberInfolist;
use Modules\Library\Filament\Resources\Members\Tables\MembersTable;
use Modules\Library\Models\Member;

class MemberResource extends LocalizedResource
{
    protected static ?string $model = Member::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MemberForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MemberInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MembersTable::configure($table);
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
            'index' => ListMembers::route('/'),
            'create' => CreateMember::route('/create'),
            'view' => ViewMember::route('/{record}'),
            'edit' => EditMember::route('/{record}/edit'),
        ];
    }
}
