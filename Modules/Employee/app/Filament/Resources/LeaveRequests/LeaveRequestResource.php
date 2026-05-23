<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Employee\Filament\Resources\LeaveRequests\Pages\CreateLeaveRequest;
use Modules\Employee\Filament\Resources\LeaveRequests\Pages\EditLeaveRequest;
use Modules\Employee\Filament\Resources\LeaveRequests\Pages\ListLeaveRequests;
use Modules\Employee\Filament\Resources\LeaveRequests\Pages\ViewLeaveRequest;
use Modules\Employee\Filament\Resources\LeaveRequests\Schemas\LeaveRequestForm;
use Modules\Employee\Filament\Resources\LeaveRequests\Schemas\LeaveRequestInfolist;
use Modules\Employee\Filament\Resources\LeaveRequests\Tables\LeaveRequestsTable;
use Modules\Employee\Models\LeaveRequest;

class LeaveRequestResource extends LocalizedResource
{
    protected static ?string $model = LeaveRequest::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LeaveRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeaveRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaveRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\WorkflowInstancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeaveRequests::route('/'),
            'create' => CreateLeaveRequest::route('/create'),
            'view' => ViewLeaveRequest::route('/{record}'),
            'edit' => EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
