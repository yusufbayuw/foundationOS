<?php

namespace Modules\Alumni\Filament\Resources\JobPostings;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Alumni\Filament\Resources\JobPostings\Pages\CreateJobPosting;
use Modules\Alumni\Filament\Resources\JobPostings\Pages\EditJobPosting;
use Modules\Alumni\Filament\Resources\JobPostings\Pages\ListJobPostings;
use Modules\Alumni\Filament\Resources\JobPostings\Pages\ViewJobPosting;
use Modules\Alumni\Filament\Resources\JobPostings\Schemas\JobPostingForm;
use Modules\Alumni\Filament\Resources\JobPostings\Schemas\JobPostingInfolist;
use Modules\Alumni\Filament\Resources\JobPostings\Tables\JobPostingsTable;
use Modules\Alumni\Models\JobPosting;
use Modules\Core\Filament\Support\ModuleResource;

class JobPostingResource extends ModuleResource
{
    protected static ?string $model = JobPosting::class;

    public static function form(Schema $schema): Schema
    {
        return JobPostingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return JobPostingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobPostingsTable::configure($table);
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
            'index' => ListJobPostings::route('/'),
            'create' => CreateJobPosting::route('/create'),
            'view' => ViewJobPosting::route('/{record}'),
            'edit' => EditJobPosting::route('/{record}/edit'),
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
