<?php

namespace Modules\Member\Filament\Resources\Members;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Member\Filament\Resources\Members\Pages\ListMembers;
use Modules\Member\Filament\Resources\Members\Pages\ViewMember;
use Modules\Member\Filament\Resources\Members\RelationManagers\ProofsRelationManager;
use Modules\Member\Filament\Resources\Members\Schemas\MemberInfolist;
use Modules\Member\Filament\Resources\Members\Tables\MembersTable;
use Modules\Member\Models\Member;

class MemberResource extends ModuleResource
{
    protected static ?string $model = Member::class;

    protected static ?string $recordTitleAttribute = 'member_number';

    public static function canCreate(): bool
    {
        return false;
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
            ProofsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMembers::route('/'),
            'view' => ViewMember::route('/{record}'),
        ];
    }
}
