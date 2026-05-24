<?php

namespace Modules\Core\Filament\Resources\ParentTeacherMessages;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\ParentTeacherMessages\Pages\CreateParentTeacherMessage;
use Modules\Core\Filament\Resources\ParentTeacherMessages\Pages\EditParentTeacherMessage;
use Modules\Core\Filament\Resources\ParentTeacherMessages\Pages\ListParentTeacherMessages;
use Modules\Core\Filament\Resources\ParentTeacherMessages\Pages\ViewParentTeacherMessage;
use Modules\Core\Filament\Resources\ParentTeacherMessages\Schemas\ParentTeacherMessageForm;
use Modules\Core\Filament\Resources\ParentTeacherMessages\Schemas\ParentTeacherMessageInfolist;
use Modules\Core\Filament\Resources\ParentTeacherMessages\Tables\ParentTeacherMessagesTable;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Core\Models\ParentTeacherMessage;

class ParentTeacherMessageResource extends ModuleResource
{
    protected static ?string $model = ParentTeacherMessage::class;

    public static function form(Schema $schema): Schema
    {
        return ParentTeacherMessageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ParentTeacherMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParentTeacherMessagesTable::configure($table);
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
            'index' => ListParentTeacherMessages::route('/'),
            'create' => CreateParentTeacherMessage::route('/create'),
            'view' => ViewParentTeacherMessage::route('/{record}'),
            'edit' => EditParentTeacherMessage::route('/{record}/edit'),
        ];
    }
}
