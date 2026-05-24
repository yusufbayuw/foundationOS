<?php

namespace Modules\EOffice\Filament\Resources\LetterAttachments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EOffice\Filament\Resources\LetterAttachments\Pages\CreateLetterAttachment;
use Modules\EOffice\Filament\Resources\LetterAttachments\Pages\EditLetterAttachment;
use Modules\EOffice\Filament\Resources\LetterAttachments\Pages\ListLetterAttachments;
use Modules\EOffice\Filament\Resources\LetterAttachments\Pages\ViewLetterAttachment;
use Modules\EOffice\Filament\Resources\LetterAttachments\Schemas\LetterAttachmentForm;
use Modules\EOffice\Filament\Resources\LetterAttachments\Schemas\LetterAttachmentInfolist;
use Modules\EOffice\Filament\Resources\LetterAttachments\Tables\LetterAttachmentsTable;
use Modules\EOffice\Models\LetterAttachment;

class LetterAttachmentResource extends ModuleResource
{
    protected static ?string $model = LetterAttachment::class;

    public static function form(Schema $schema): Schema
    {
        return LetterAttachmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LetterAttachmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LetterAttachmentsTable::configure($table);
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
            'index' => ListLetterAttachments::route('/'),
            'create' => CreateLetterAttachment::route('/create'),
            'view' => ViewLetterAttachment::route('/{record}'),
            'edit' => EditLetterAttachment::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
