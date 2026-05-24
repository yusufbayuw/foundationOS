<?php

namespace Modules\ItOps\Filament\Resources\BackupJobs;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\ItOps\Filament\Resources\BackupJobs\Pages\CreateBackupJob;
use Modules\ItOps\Filament\Resources\BackupJobs\Pages\EditBackupJob;
use Modules\ItOps\Filament\Resources\BackupJobs\Pages\ListBackupJobs;
use Modules\ItOps\Filament\Resources\BackupJobs\Pages\ViewBackupJob;
use Modules\ItOps\Filament\Resources\BackupJobs\Schemas\BackupJobForm;
use Modules\ItOps\Filament\Resources\BackupJobs\Schemas\BackupJobInfolist;
use Modules\ItOps\Filament\Resources\BackupJobs\Tables\BackupJobsTable;
use Modules\ItOps\Models\BackupJob;

class BackupJobResource extends ModuleResource
{
    protected static ?string $model = BackupJob::class;

    public static function form(Schema $schema): Schema
    {
        return BackupJobForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BackupJobInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BackupJobsTable::configure($table);
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
            'index' => ListBackupJobs::route('/'),
            'create' => CreateBackupJob::route('/create'),
            'view' => ViewBackupJob::route('/{record}'),
            'edit' => EditBackupJob::route('/{record}/edit'),
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
