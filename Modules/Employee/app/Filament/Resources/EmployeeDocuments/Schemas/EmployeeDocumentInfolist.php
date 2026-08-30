<?php

namespace Modules\Employee\Filament\Resources\EmployeeDocuments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;
use Modules\Employee\Models\EmployeeDocument;

class EmployeeDocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(FilamentUi::text('Tenant')),
                TextEntry::make('employee.id')
                    ->label(FilamentUi::text('Employee')),
                TextEntry::make('document_type'),
                TextEntry::make('document_number')
                    ->placeholder('-'),
                TextEntry::make('file_path'),
                TextEntry::make('original_filename')
                    ->placeholder('-'),
                TextEntry::make('issue_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('expiry_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('verification_status'),
                TextEntry::make('verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('verified_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (EmployeeDocument $record): bool => $record->trashed()),
            ]);
    }
}
