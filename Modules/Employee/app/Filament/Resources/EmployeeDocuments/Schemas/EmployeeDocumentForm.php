<?php

namespace Modules\Employee\Filament\Resources\EmployeeDocuments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('employee_id')
                    ->relationship('employee', 'id')
                    ->required(),
                TextInput::make('document_type')
                    ->required(),
                TextInput::make('document_number'),
                TextInput::make('file_path')
                    ->required(),
                TextInput::make('original_filename'),
                DatePicker::make('issue_date'),
                DatePicker::make('expiry_date'),
                TextInput::make('verification_status')
                    ->required()
                    ->default('unverified'),
                DateTimePicker::make('verified_at'),
                TextInput::make('verified_by')
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
