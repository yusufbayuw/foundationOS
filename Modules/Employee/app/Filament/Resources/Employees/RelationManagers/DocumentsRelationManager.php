<?php

namespace Modules\Employee\Filament\Resources\Employees\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
use Modules\Employee\Models\EmployeeDocument;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('document_type')
                ->label(FilamentUi::field('document_type'))
                ->options(EmployeeDocument::documentTypeOptions())
                ->required(),

            TextInput::make('document_number')
                ->label(FilamentUi::field('document_number'))
                ->placeholder(FilamentUi::text('e.g. NIK, NPWP number')),

            FileUpload::make('file_path')
                ->label(FilamentUi::field('file_path'))
                ->required()
                ->directory('employee-documents')
                ->visibility('private')
                ->maxSize(5120)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                ->storeFileNamesIn('original_filename')
                ->columnSpanFull(),

            DatePicker::make('issue_date')
                ->label(FilamentUi::field('issue_date')),

            DatePicker::make('expiry_date')
                ->label(FilamentUi::field('expiry_date')),

            Select::make('verification_status')
                ->label(FilamentUi::field('verification_status'))
                ->options([
                    'unverified' => FilamentUi::text('Unverified'),
                    'verified' => FilamentUi::text('Verified'),
                    'expired' => FilamentUi::text('Expired'),
                ])
                ->default('unverified')
                ->required(),

            Textarea::make('notes')
                ->label(FilamentUi::field('notes'))
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('document_type')
                    ->label(FilamentUi::field('document_type'))
                    ->formatStateUsing(fn (string $state): string => EmployeeDocument::documentTypeOptions()[$state] ?? $state)
                    ->searchable(),

                Tables\Columns\TextColumn::make('document_number')
                    ->label(FilamentUi::field('document_number'))
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label(FilamentUi::field('expiry_date'))
                    ->date()
                    ->placeholder('-')
                    ->color(fn (EmployeeDocument $record): string => $record->isExpired() ? 'danger' : 'default'),

                Tables\Columns\BadgeColumn::make('verification_status')
                    ->label(FilamentUi::field('verification_status'))
                    ->colors([
                        'success' => 'verified',
                        'warning' => 'unverified',
                        'danger' => 'expired',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['tenant_id'] = filament()->getTenant()?->id;

                        return $data;
                    }),
            ])
            ->recordActions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('verify')
                    ->label(FilamentUi::text('Verify'))
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->authorize('update')
                    ->visible(fn (EmployeeDocument $record): bool => $record->verification_status !== 'verified')
                    ->requiresConfirmation()
                    ->action(function (EmployeeDocument $record): void {
                        $record->update([
                            'verification_status' => 'verified',
                            'verified_at' => now(),
                            'verified_by' => auth()->id(),
                        ]);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
