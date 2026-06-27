<?php

namespace Modules\Campus\Filament\Resources\CollageStudents;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Campus\Filament\Resources\CollageStudents\Pages\CreateCollageStudent;
use Modules\Campus\Filament\Resources\CollageStudents\Pages\EditCollageStudent;
use Modules\Campus\Filament\Resources\CollageStudents\Pages\ListCollageStudents;
use Modules\Campus\Filament\Resources\CollageStudents\Pages\ViewCollageStudent;
use Modules\Campus\Filament\Resources\CollageStudents\RelationManagers\AuditLogsRelationManager;
use Modules\Campus\Filament\Resources\CollageStudents\RelationManagers\FileUploadsRelationManager;
use Modules\Campus\Filament\Resources\CollageStudents\RelationManagers\StudyPlansRelationManager;
use Modules\Campus\Filament\Resources\CollageStudents\RelationManagers\ThesesRelationManager;
use Modules\Campus\Filament\Resources\CollageStudents\Schemas\CollageStudentForm;
use Modules\Campus\Filament\Resources\CollageStudents\Schemas\CollageStudentInfolist;
use Modules\Campus\Filament\Resources\CollageStudents\Tables\CollageStudentsTable;
use Modules\Campus\Models\CollageStudent;
use Modules\Core\Filament\Concerns\ConfiguresGlobalSearch;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;

class CollageStudentResource extends LocalizedResource
{
    use ConfiguresGlobalSearch;

    protected static ?string $model = CollageStudent::class;

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static function globalSearchAttributes(): array
    {
        return ['student_number', 'full_name', 'email'];
    }

    protected static function globalSearchResultDetails(Model $record): array
    {
        assert($record instanceof CollageStudent);

        return static::detailStatus($record->status);
    }

    public static function form(Schema $schema): Schema
    {
        return CollageStudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CollageStudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CollageStudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StudyPlansRelationManager::class,
            ThesesRelationManager::class,
            AuditLogsRelationManager::class,
            FileUploadsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCollageStudents::route('/'),
            'create' => CreateCollageStudent::route('/create'),
            'view' => ViewCollageStudent::route('/{record}'),
            'edit' => EditCollageStudent::route('/{record}/edit'),
        ];
    }
}
