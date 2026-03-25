<?php

namespace Modules\Enrollment\Filament\Resources\Applicants;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Enrollment\Filament\Resources\Applicants\Pages\CreateApplicant;
use Modules\Enrollment\Filament\Resources\Applicants\Pages\EditApplicant;
use Modules\Enrollment\Filament\Resources\Applicants\Pages\ListApplicants;
use Modules\Enrollment\Filament\Resources\Applicants\Pages\ViewApplicant;
use Modules\Enrollment\Filament\Resources\Applicants\Schemas\ApplicantForm;
use Modules\Enrollment\Filament\Resources\Applicants\Schemas\ApplicantInfolist;
use Modules\Enrollment\Filament\Resources\Applicants\Tables\ApplicantsTable;
use Modules\Enrollment\Models\Applicant;

class ApplicantResource extends LocalizedResource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ApplicantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ApplicantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApplicantsTable::configure($table);
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
            'index' => ListApplicants::route('/'),
            'create' => CreateApplicant::route('/create'),
            'view' => ViewApplicant::route('/{record}'),
            'edit' => EditApplicant::route('/{record}/edit'),
        ];
    }
}
