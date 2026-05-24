<?php

namespace Modules\EOffice\Filament\Resources\LetterTemplates;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EOffice\Filament\Resources\LetterTemplates\Pages\CreateLetterTemplate;
use Modules\EOffice\Filament\Resources\LetterTemplates\Pages\EditLetterTemplate;
use Modules\EOffice\Filament\Resources\LetterTemplates\Pages\ListLetterTemplates;
use Modules\EOffice\Filament\Resources\LetterTemplates\Pages\ViewLetterTemplate;
use Modules\EOffice\Filament\Resources\LetterTemplates\Schemas\LetterTemplateForm;
use Modules\EOffice\Filament\Resources\LetterTemplates\Schemas\LetterTemplateInfolist;
use Modules\EOffice\Filament\Resources\LetterTemplates\Tables\LetterTemplatesTable;
use Modules\EOffice\Models\LetterTemplate;

class LetterTemplateResource extends ModuleResource
{
    protected static ?string $model = LetterTemplate::class;

    public static function form(Schema $schema): Schema
    {
        return LetterTemplateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LetterTemplateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LetterTemplatesTable::configure($table);
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
            'index' => ListLetterTemplates::route('/'),
            'create' => CreateLetterTemplate::route('/create'),
            'view' => ViewLetterTemplate::route('/{record}'),
            'edit' => EditLetterTemplate::route('/{record}/edit'),
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
