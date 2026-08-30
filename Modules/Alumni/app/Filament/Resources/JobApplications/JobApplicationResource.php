<?php

namespace Modules\Alumni\Filament\Resources\JobApplications;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Alumni\Filament\Resources\JobApplications\Pages\EditJobApplication;
use Modules\Alumni\Filament\Resources\JobApplications\Pages\ListJobApplications;
use Modules\Alumni\Filament\Resources\JobApplications\Pages\ViewJobApplication;
use Modules\Alumni\Filament\Resources\JobApplications\Schemas\JobApplicationForm;
use Modules\Alumni\Filament\Resources\JobApplications\Schemas\JobApplicationInfolist;
use Modules\Alumni\Filament\Resources\JobApplications\Tables\JobApplicationsTable;
use Modules\Alumni\Models\JobApplication;
use Modules\Core\Filament\Support\ModuleResource;

class JobApplicationResource extends ModuleResource
{
    protected static ?string $model = JobApplication::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return JobApplicationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return JobApplicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobApplicationsTable::configure($table);
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
            'index' => ListJobApplications::route('/'),
            'view' => ViewJobApplication::route('/{record}'),
            'edit' => EditJobApplication::route('/{record}/edit'),
        ];
    }
}
