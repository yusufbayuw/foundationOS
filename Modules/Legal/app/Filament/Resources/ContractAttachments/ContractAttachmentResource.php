<?php

namespace Modules\Legal\Filament\Resources\ContractAttachments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Legal\Filament\Resources\ContractAttachments\Pages\CreateContractAttachment;
use Modules\Legal\Filament\Resources\ContractAttachments\Pages\EditContractAttachment;
use Modules\Legal\Filament\Resources\ContractAttachments\Pages\ListContractAttachments;
use Modules\Legal\Filament\Resources\ContractAttachments\Pages\ViewContractAttachment;
use Modules\Legal\Filament\Resources\ContractAttachments\Schemas\ContractAttachmentForm;
use Modules\Legal\Filament\Resources\ContractAttachments\Schemas\ContractAttachmentInfolist;
use Modules\Legal\Filament\Resources\ContractAttachments\Tables\ContractAttachmentsTable;
use Modules\Legal\Models\ContractAttachment;

class ContractAttachmentResource extends ModuleResource
{
    protected static ?string $model = ContractAttachment::class;

    public static function form(Schema $schema): Schema
    {
        return ContractAttachmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContractAttachmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContractAttachmentsTable::configure($table);
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
            'index' => ListContractAttachments::route('/'),
            'create' => CreateContractAttachment::route('/create'),
            'view' => ViewContractAttachment::route('/{record}'),
            'edit' => EditContractAttachment::route('/{record}/edit'),
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
