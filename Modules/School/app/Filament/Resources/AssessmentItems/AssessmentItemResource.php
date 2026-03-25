<?php

namespace Modules\School\Filament\Resources\AssessmentItems;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\AssessmentItems\Pages\CreateAssessmentItem;
use Modules\School\Filament\Resources\AssessmentItems\Pages\EditAssessmentItem;
use Modules\School\Filament\Resources\AssessmentItems\Pages\ListAssessmentItems;
use Modules\School\Filament\Resources\AssessmentItems\Pages\ViewAssessmentItem;
use Modules\School\Filament\Resources\AssessmentItems\Schemas\AssessmentItemForm;
use Modules\School\Filament\Resources\AssessmentItems\Schemas\AssessmentItemInfolist;
use Modules\School\Filament\Resources\AssessmentItems\Tables\AssessmentItemsTable;
use Modules\School\Models\AssessmentItem;

class AssessmentItemResource extends LocalizedResource
{
    protected static ?string $model = AssessmentItem::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AssessmentItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssessmentItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssessmentItemsTable::configure($table);
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
            'index' => ListAssessmentItems::route('/'),
            'create' => CreateAssessmentItem::route('/create'),
            'view' => ViewAssessmentItem::route('/{record}'),
            'edit' => EditAssessmentItem::route('/{record}/edit'),
        ];
    }
}
