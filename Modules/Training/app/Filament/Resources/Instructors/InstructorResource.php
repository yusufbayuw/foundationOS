<?php

namespace Modules\Training\Filament\Resources\Instructors;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Training\Filament\Resources\Instructors\Pages\CreateInstructor;
use Modules\Training\Filament\Resources\Instructors\Pages\EditInstructor;
use Modules\Training\Filament\Resources\Instructors\Pages\ListInstructors;
use Modules\Training\Filament\Resources\Instructors\Pages\ViewInstructor;
use Modules\Training\Filament\Resources\Instructors\Schemas\InstructorForm;
use Modules\Training\Filament\Resources\Instructors\Schemas\InstructorInfolist;
use Modules\Training\Filament\Resources\Instructors\Tables\InstructorsTable;
use Modules\Training\Models\Instructor;

class InstructorResource extends ModuleResource
{
    protected static ?string $model = Instructor::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return InstructorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InstructorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InstructorsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInstructors::route('/'),
            'create' => CreateInstructor::route('/create'),
            'view' => ViewInstructor::route('/{record}'),
            'edit' => EditInstructor::route('/{record}/edit'),
        ];
    }
}
