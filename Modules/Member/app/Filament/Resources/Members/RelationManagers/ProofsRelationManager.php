<?php

namespace Modules\Member\Filament\Resources\Members\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Member\Models\MemberProof;

class ProofsRelationManager extends RelationManager
{
    protected static string $relationship = 'proofs';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('file_path'),
                TextEntry::make('mime_type')->label(FilamentUi::text('File type')),
                TextEntry::make('status')->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_path')
            ->columns([
                TextColumn::make('file_path')
                    ->label(FilamentUi::text('File'))
                    ->copyable()
                    ->searchable(),
                TextColumn::make('mime_type')
                    ->label(FilamentUi::text('File type')),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('approve')
                    ->icon(Heroicon::CheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (MemberProof $record): bool => $record->status !== 'approved')
                    ->authorize(fn (MemberProof $record): bool => auth()->user()?->can('update', $record->member) ?? false)
                    ->action(function (MemberProof $record): void {
                        $record->update(['status' => 'approved']);

                        Notification::make()->title('Proof approved')->success()->send();
                    }),
                Action::make('reject')
                    ->icon(Heroicon::XCircle)
                    ->color('danger')
                    ->schema([
                        Textarea::make('reason')
                            ->helperText(FilamentUi::text('Reason is recorded in the activity log for the parent membership.'))
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->visible(fn (MemberProof $record): bool => $record->status !== 'rejected')
                    ->authorize(fn (MemberProof $record): bool => auth()->user()?->can('update', $record->member) ?? false)
                    ->action(function (array $data, MemberProof $record): void {
                        $user = auth()->user();

                        abort_unless($user instanceof User, 403);

                        $record->update(['status' => 'rejected']);

                        activity()
                            ->performedOn($record->member)
                            ->causedBy($user)
                            ->withProperties(['proof_id' => $record->getKey(), 'reason' => $data['reason']])
                            ->log('Member proof rejected');

                        Notification::make()->title('Proof rejected')->danger()->send();
                    }),
            ]);
    }
}
