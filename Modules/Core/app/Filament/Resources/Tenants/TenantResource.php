<?php

namespace Modules\Core\Filament\Resources\Tenants;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Tenants\Pages\CreateTenant;
use Modules\Core\Filament\Resources\Tenants\Pages\EditTenant;
use Modules\Core\Filament\Resources\Tenants\Pages\ListTenants;
use Modules\Core\Filament\Resources\Tenants\Pages\ViewTenant;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\AcademicYearsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\AchievementTypesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\AssessmentsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\AssessmentItemsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\AttachedFilesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\AuditLogsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\AuditableLogsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\AttendancesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\ClassStudentsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\CollageStudentsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\CourseOfferingsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\CoursesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\CurriculaRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\DepartmentsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\FacultiesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\FeederLogsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\FileUploadsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\LecturersRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\ModulesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\OrganizationsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\SchoolClassesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\SchedulesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\StudentAchievementsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\StudentAssessmentAnswersRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\StudentGradesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\StudentsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\StudyPlanItemsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\StudyPlansRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\StudyProgramsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\StudyResultsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\SubjectsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\SubscriptionLogsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\TeachersRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\TenantModulesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\TenantRolesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\TenantSettingsRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\ThesesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\UserTenantRolesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\UsersRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\ViolationTypesRelationManager;
use Modules\Core\Filament\Resources\Tenants\RelationManagers\ViolationsRelationManager;
use Modules\Core\Filament\Resources\Tenants\Schemas\TenantForm;
use Modules\Core\Filament\Resources\Tenants\Schemas\TenantInfolist;
use Modules\Core\Filament\Resources\Tenants\Tables\TenantsTable;
use Modules\Core\Models\Tenant;

class TenantResource extends LocalizedResource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TenantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TenantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OrganizationsRelationManager::class,
            TenantRolesRelationManager::class,
            UserTenantRolesRelationManager::class,
            SubscriptionLogsRelationManager::class,
            TenantSettingsRelationManager::class,
            AcademicYearsRelationManager::class,
            DepartmentsRelationManager::class,
            TenantModulesRelationManager::class,
            ModulesRelationManager::class,
            UsersRelationManager::class,
            CurriculaRelationManager::class,
            SubjectsRelationManager::class,
            StudentsRelationManager::class,
            TeachersRelationManager::class,
            SchoolClassesRelationManager::class,
            ClassStudentsRelationManager::class,
            SchedulesRelationManager::class,
            AttendancesRelationManager::class,
            AssessmentsRelationManager::class,
            AssessmentItemsRelationManager::class,
            StudentAssessmentAnswersRelationManager::class,
            StudentGradesRelationManager::class,
            ViolationTypesRelationManager::class,
            ViolationsRelationManager::class,
            AchievementTypesRelationManager::class,
            StudentAchievementsRelationManager::class,
            FacultiesRelationManager::class,
            StudyProgramsRelationManager::class,
            CoursesRelationManager::class,
            LecturersRelationManager::class,
            CollageStudentsRelationManager::class,
            CourseOfferingsRelationManager::class,
            StudyPlansRelationManager::class,
            StudyPlanItemsRelationManager::class,
            StudyResultsRelationManager::class,
            FeederLogsRelationManager::class,
            ThesesRelationManager::class,
            AuditLogsRelationManager::class,
            FileUploadsRelationManager::class,
            AuditableLogsRelationManager::class,
            AttachedFilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'view' => ViewTenant::route('/{record}'),
            'edit' => EditTenant::route('/{record}/edit'),
        ];
    }
}
