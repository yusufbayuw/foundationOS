<?php

namespace Modules\School\Filament\Resources\Students;

use App\Support\TypedValue;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Concerns\ConfiguresGlobalSearch;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Core\Support\FilamentUi;
use Modules\School\Filament\Resources\Students\Pages\CreateStudent;
use Modules\School\Filament\Resources\Students\Pages\EditStudent;
use Modules\School\Filament\Resources\Students\Pages\ListStudents;
use Modules\School\Filament\Resources\Students\Pages\ViewStudent;
use Modules\School\Filament\Resources\Students\RelationManagers\AttendancesRelationManager;
use Modules\School\Filament\Resources\Students\RelationManagers\AuditLogsRelationManager;
use Modules\School\Filament\Resources\Students\RelationManagers\ClassStudentsRelationManager;
use Modules\School\Filament\Resources\Students\RelationManagers\FileUploadsRelationManager;
use Modules\School\Filament\Resources\Students\RelationManagers\StudentAchievementsRelationManager;
use Modules\School\Filament\Resources\Students\RelationManagers\StudentAssessmentAnswersRelationManager;
use Modules\School\Filament\Resources\Students\RelationManagers\StudentGradesRelationManager;
use Modules\School\Filament\Resources\Students\RelationManagers\ViolationsRelationManager;
use Modules\School\Filament\Resources\Students\Schemas\StudentForm;
use Modules\School\Filament\Resources\Students\Schemas\StudentInfolist;
use Modules\School\Filament\Resources\Students\Tables\StudentsTable;
use Modules\School\Models\Student;

class StudentResource extends LocalizedResource
{
    use ConfiguresGlobalSearch;

    protected static ?string $model = Student::class;

    /**
     * @return array<int, string>
     */
    protected static function globalSearchAttributes(): array
    {
        return ['nis', 'nisn', 'user.name'];
    }

    /**
     * @return array<string, string>
     */
    protected static function globalSearchResultDetails(Model $record): array
    {
        assert($record instanceof Student);

        return array_merge(
            static::detailStatus($record->status),
            [FilamentUi::field('nis') => $record->nis ?? '-'],
        );
    }

    protected static ?string $recordTitleAttribute = null;

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if (! $record instanceof Student) {
            return null;
        }

        return $record->user->name
            ?? ($record->nis ? 'NIS: '.$record->nis : null)
            ?? 'Siswa #'.TypedValue::string($record->getKey());
    }

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ClassStudentsRelationManager::class,
            AttendancesRelationManager::class,
            StudentGradesRelationManager::class,
            ViolationsRelationManager::class,
            StudentAssessmentAnswersRelationManager::class,
            StudentAchievementsRelationManager::class,
            AuditLogsRelationManager::class,
            FileUploadsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}
