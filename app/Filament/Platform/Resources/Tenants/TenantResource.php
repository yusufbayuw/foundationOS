<?php

namespace App\Filament\Platform\Resources\Tenants;

use App\Filament\Platform\Resources\Tenants\Pages\CreateTenant;
use App\Filament\Platform\Resources\Tenants\Pages\EditTenant;
use App\Filament\Platform\Resources\Tenants\Pages\ListTenants;
use App\Filament\Platform\Resources\Tenants\Pages\ViewTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Resources\Tenants\Schemas\TenantForm;
use Modules\Core\Filament\Resources\Tenants\Schemas\TenantInfolist;
use Modules\Core\Models\Tenant;
use Modules\Core\Support\FilamentUi;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::BuildingOffice2;

    protected static ?string $navigationLabel = 'Tenants';

    protected static \UnitEnum|string|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 10;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'suspended' => 'danger',
                        'trial' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('users_count')
                    ->counts('users')
                    ->label(FilamentUi::text('Users'))
                    ->sortable(),

                TextColumn::make('tenantModules_count')
                    ->counts('tenantModules')
                    ->label(FilamentUi::text('Modules'))
                    ->sortable(),

                TextColumn::make('subscription_plan_id')
                    ->label(FilamentUi::text('Plan'))
                    ->default('—'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->label(FilamentUi::text('Registered')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function form(Schema $schema): Schema
    {
        return TenantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TenantInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'view' => ViewTenant::route('/{record}'),
            'edit' => EditTenant::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return static::canManagePlatformTenants();
    }

    public static function canViewAny(): bool
    {
        return static::canManagePlatformTenants();
    }

    public static function canView(Model $record): bool
    {
        return static::canManagePlatformTenants();
    }

    public static function canCreate(): bool
    {
        return static::canManagePlatformTenants();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canManagePlatformTenants();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canManagePlatformTenants();
    }

    private static function canManagePlatformTenants(): bool
    {
        $user = auth()->user();

        if ($user === null) {
            return false;
        }

        if (Filament::getCurrentPanel()?->getId() === 'platform') {
            return true;
        }

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId(0);
        }

        return $user->hasRole('platform_owner');
    }
}
