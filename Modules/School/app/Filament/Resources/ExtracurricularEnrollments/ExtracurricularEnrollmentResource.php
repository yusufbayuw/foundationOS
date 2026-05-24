<?php

namespace Modules\School\Filament\Resources\ExtracurricularEnrollments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\School\Filament\Resources\ExtracurricularEnrollments\Pages\CreateExtracurricularEnrollment;
use Modules\School\Filament\Resources\ExtracurricularEnrollments\Pages\EditExtracurricularEnrollment;
use Modules\School\Filament\Resources\ExtracurricularEnrollments\Pages\ListExtracurricularEnrollments;
use Modules\School\Filament\Resources\ExtracurricularEnrollments\Pages\ViewExtracurricularEnrollment;
use Modules\School\Filament\Resources\ExtracurricularEnrollments\Schemas\ExtracurricularEnrollmentForm;
use Modules\School\Filament\Resources\ExtracurricularEnrollments\Schemas\ExtracurricularEnrollmentInfolist;
use Modules\School\Filament\Resources\ExtracurricularEnrollments\Tables\ExtracurricularEnrollmentsTable;
use Modules\School\Models\ExtracurricularEnrollment;

class ExtracurricularEnrollmentResource extends ModuleResource
{
    protected static ?string $model = ExtracurricularEnrollment::class;

    protected static ?string $recordTitleAttribute = 'status';

    public static function form(Schema $schema): Schema
    {
        return ExtracurricularEnrollmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExtracurricularEnrollmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExtracurricularEnrollmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExtracurricularEnrollments::route('/'),
            'create' => CreateExtracurricularEnrollment::route('/create'),
            'view' => ViewExtracurricularEnrollment::route('/{record}'),
            'edit' => EditExtracurricularEnrollment::route('/{record}/edit'),
        ];
    }
}
