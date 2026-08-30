<?php

namespace Modules\Member\Filament\Resources\Members\Tables;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Member\Models\Member;
use Modules\Member\Models\MemberType;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(FilamentUi::text('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label(FilamentUi::text('Email'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('member_number')
                    ->label(FilamentUi::text('Member number'))
                    ->searchable()
                    ->copyable(),
                TextColumn::make('memberType.name')
                    ->label(FilamentUi::text('Member type'))
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('verified_at')
                    ->label(FilamentUi::text('Reviewed at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(FilamentUi::text('Registered at'))
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('domain_member_type_id')
                    ->label(FilamentUi::text('Member type'))
                    ->options(fn (): array => MemberType::query()->orderBy('name')->pluck('name', 'id')->all()),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('approve')
                    ->label(FilamentUi::text('Approve'))
                    ->icon(Heroicon::CheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Member $record): bool => $record->status !== 'approved')
                    ->authorize(fn (Member $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->action(function (Member $record): void {
                        $user = auth()->user();

                        abort_unless($user instanceof User, 403);

                        $record->approve($user);

                        Notification::make()
                            ->title('Member approved')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label(FilamentUi::text('Reject'))
                    ->icon(Heroicon::XCircle)
                    ->color('danger')
                    ->schema([
                        Textarea::make('reason')
                            ->label(FilamentUi::text('Reason'))
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->visible(fn (Member $record): bool => $record->status !== 'rejected')
                    ->authorize(fn (Member $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->action(function (array $data, Member $record): void {
                        $user = auth()->user();

                        abort_unless($user instanceof User, 403);

                        $record->reject($user, $data['reason']);

                        Notification::make()
                            ->title('Member rejected')
                            ->danger()
                            ->send();
                    }),
            ]);
    }
}
