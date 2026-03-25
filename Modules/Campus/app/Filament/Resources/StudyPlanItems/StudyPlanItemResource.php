<?php

namespace Modules\Campus\Filament\Resources\StudyPlanItems;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\StudyPlanItems\Pages\CreateStudyPlanItem;
use Modules\Campus\Filament\Resources\StudyPlanItems\Pages\EditStudyPlanItem;
use Modules\Campus\Filament\Resources\StudyPlanItems\Pages\ListStudyPlanItems;
use Modules\Campus\Filament\Resources\StudyPlanItems\Pages\ViewStudyPlanItem;
use Modules\Campus\Filament\Resources\StudyPlanItems\Schemas\StudyPlanItemForm;
use Modules\Campus\Filament\Resources\StudyPlanItems\Schemas\StudyPlanItemInfolist;
use Modules\Campus\Filament\Resources\StudyPlanItems\Tables\StudyPlanItemsTable;
use Modules\Campus\Models\StudyPlanItem;

class StudyPlanItemResource extends LocalizedResource
{
    protected static ?string $model = StudyPlanItem::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StudyPlanItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudyPlanItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudyPlanItemsTable::configure($table);
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
            'index' => ListStudyPlanItems::route('/'),
            'create' => CreateStudyPlanItem::route('/create'),
            'view' => ViewStudyPlanItem::route('/{record}'),
            'edit' => EditStudyPlanItem::route('/{record}/edit'),
        ];
    }
}
