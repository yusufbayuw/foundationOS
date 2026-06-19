<?php

namespace Modules\Enrollment\Filament\Resources\Registrations;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Enrollment\Filament\Resources\Registrations\Pages\CreateRegistration;
use Modules\Enrollment\Filament\Resources\Registrations\Pages\EditRegistration;
use Modules\Enrollment\Filament\Resources\Registrations\Pages\ListRegistrations;
use Modules\Enrollment\Filament\Resources\Registrations\Pages\ViewRegistration;
use Modules\Enrollment\Filament\Resources\Registrations\Schemas\RegistrationForm;
use Modules\Enrollment\Filament\Resources\Registrations\Schemas\RegistrationInfolist;
use Modules\Enrollment\Filament\Resources\Registrations\Tables\RegistrationsTable;
use Modules\Enrollment\Models\Registration;

class RegistrationResource extends LocalizedResource
{
    protected static ?string $model = Registration::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RegistrationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RegistrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RegistrationsTable::configure($table);
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
            'index' => ListRegistrations::route('/'),
            'create' => CreateRegistration::route('/create'),
            'view' => ViewRegistration::route('/{record}'),
            'edit' => EditRegistration::route('/{record}/edit'),
        ];
    }
}
