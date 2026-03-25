<?php

namespace Modules\Campus\Filament\Resources\CourseOfferings;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\CourseOfferings\Pages\CreateCourseOffering;
use Modules\Campus\Filament\Resources\CourseOfferings\Pages\EditCourseOffering;
use Modules\Campus\Filament\Resources\CourseOfferings\Pages\ListCourseOfferings;
use Modules\Campus\Filament\Resources\CourseOfferings\Pages\ViewCourseOffering;
use Modules\Campus\Filament\Resources\CourseOfferings\Schemas\CourseOfferingForm;
use Modules\Campus\Filament\Resources\CourseOfferings\Schemas\CourseOfferingInfolist;
use Modules\Campus\Filament\Resources\CourseOfferings\Tables\CourseOfferingsTable;
use Modules\Campus\Models\CourseOffering;

class CourseOfferingResource extends LocalizedResource
{
    protected static ?string $model = CourseOffering::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CourseOfferingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CourseOfferingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseOfferingsTable::configure($table);
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
            'index' => ListCourseOfferings::route('/'),
            'create' => CreateCourseOffering::route('/create'),
            'view' => ViewCourseOffering::route('/{record}'),
            'edit' => EditCourseOffering::route('/{record}/edit'),
        ];
    }
}
