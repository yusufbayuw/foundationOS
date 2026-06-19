<?php

namespace Modules\Core\Filament\Resources\Organizations;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Organizations\Pages\CreateOrganization;
use Modules\Core\Filament\Resources\Organizations\Pages\EditOrganization;
use Modules\Core\Filament\Resources\Organizations\Pages\ListOrganizations;
use Modules\Core\Filament\Resources\Organizations\Pages\ViewOrganization;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\AcademicPeriodsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\AcademicYearsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\AchievementTypesRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\AssessmentsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\AttachedFilesRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\AuditableLogsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\AuditLogsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\CollageStudentsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\CourseOfferingsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\CurriculaRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\DepartmentsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\FacultiesRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\FeederLogsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\FileUploadsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\LecturersRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\OrganizationSettingsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\SchedulesRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\SchoolClassesRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\StudentAchievementsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\StudentsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\StudyProgramsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\SubjectsRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\TeachersRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\UsersRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\UserTenantRolesRelationManager;
use Modules\Core\Filament\Resources\Organizations\RelationManagers\ViolationTypesRelationManager;
use Modules\Core\Filament\Resources\Organizations\Schemas\OrganizationForm;
use Modules\Core\Filament\Resources\Organizations\Schemas\OrganizationInfolist;
use Modules\Core\Filament\Resources\Organizations\Tables\OrganizationsTable;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Core\Models\Organization;

class OrganizationResource extends LocalizedResource
{
    protected static ?string $model = Organization::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OrganizationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrganizationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OrganizationSettingsRelationManager::class,
            UserTenantRolesRelationManager::class,
            AcademicYearsRelationManager::class,
            AcademicPeriodsRelationManager::class,
            DepartmentsRelationManager::class,
            UsersRelationManager::class,
            CurriculaRelationManager::class,
            SubjectsRelationManager::class,
            StudentsRelationManager::class,
            TeachersRelationManager::class,
            SchoolClassesRelationManager::class,
            SchedulesRelationManager::class,
            AssessmentsRelationManager::class,
            AchievementTypesRelationManager::class,
            StudentAchievementsRelationManager::class,
            ViolationTypesRelationManager::class,
            FacultiesRelationManager::class,
            StudyProgramsRelationManager::class,
            LecturersRelationManager::class,
            CollageStudentsRelationManager::class,
            CourseOfferingsRelationManager::class,
            FeederLogsRelationManager::class,
            AuditLogsRelationManager::class,
            FileUploadsRelationManager::class,
            AuditableLogsRelationManager::class,
            AttachedFilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'create' => CreateOrganization::route('/create'),
            'view' => ViewOrganization::route('/{record}'),
            'edit' => EditOrganization::route('/{record}/edit'),
        ];
    }
}
