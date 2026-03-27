<?php

namespace Modules\Core\Filament\Resources\Users;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Users\Pages\CreateUser;
use Modules\Core\Filament\Resources\Users\Pages\EditUser;
use Modules\Core\Filament\Resources\Users\Pages\ListUsers;
use Modules\Core\Filament\Resources\Users\Pages\ViewUser;
use Modules\Core\Filament\Resources\Users\RelationManagers\ApprovedStudyPlansRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\AssignedTenantRolesRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\AuditLogsRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\CreatedTenantsRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\GradedStudentAnswersRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\GradedStudentGradesRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\HandledViolationsRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\LecturersRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\OrganizationsRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\PrincipalOrganizationsRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\ReportedViolationsRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\StudentsRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\SyncedFeederLogsRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\TeachersRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\TenantsRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\UploadedFilesRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\UserTenantRolesRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\VerifiedAttendancesRelationManager;
use Modules\Core\Filament\Resources\Users\RelationManagers\VerifiedStudentAchievementsRelationManager;
use Modules\Core\Filament\Resources\Users\Schemas\UserForm;
use Modules\Core\Filament\Resources\Users\Schemas\UserInfolist;
use Modules\Core\Filament\Resources\Users\Tables\UsersTable;
use Modules\Core\Models\User;

class UserResource extends LocalizedResource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $tenantOwnershipRelationshipName = 'tenants';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            UserTenantRolesRelationManager::class,
            AssignedTenantRolesRelationManager::class,
            TenantsRelationManager::class,
            OrganizationsRelationManager::class,
            CreatedTenantsRelationManager::class,
            PrincipalOrganizationsRelationManager::class,
            StudentsRelationManager::class,
            TeachersRelationManager::class,
            LecturersRelationManager::class,
            VerifiedAttendancesRelationManager::class,
            ReportedViolationsRelationManager::class,
            HandledViolationsRelationManager::class,
            GradedStudentAnswersRelationManager::class,
            GradedStudentGradesRelationManager::class,
            VerifiedStudentAchievementsRelationManager::class,
            ApprovedStudyPlansRelationManager::class,
            SyncedFeederLogsRelationManager::class,
            AuditLogsRelationManager::class,
            UploadedFilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
