<?php

namespace Modules\Alumni\Filament\Resources\MentoringSessions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Alumni\Filament\Resources\MentoringSessions\Pages\CreateMentoringSession;
use Modules\Alumni\Filament\Resources\MentoringSessions\Pages\EditMentoringSession;
use Modules\Alumni\Filament\Resources\MentoringSessions\Pages\ListMentoringSessions;
use Modules\Alumni\Filament\Resources\MentoringSessions\Pages\ViewMentoringSession;
use Modules\Alumni\Filament\Resources\MentoringSessions\Schemas\MentoringSessionForm;
use Modules\Alumni\Filament\Resources\MentoringSessions\Schemas\MentoringSessionInfolist;
use Modules\Alumni\Filament\Resources\MentoringSessions\Tables\MentoringSessionsTable;
use Modules\Alumni\Models\MentoringSession;
use Modules\Core\Filament\Support\ModuleResource;

class MentoringSessionResource extends ModuleResource
{
    protected static ?string $model = MentoringSession::class;

    public static function form(Schema $schema): Schema
    {
        return MentoringSessionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MentoringSessionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MentoringSessionsTable::configure($table);
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
            'index' => ListMentoringSessions::route('/'),
            'create' => CreateMentoringSession::route('/create'),
            'view' => ViewMentoringSession::route('/{record}'),
            'edit' => EditMentoringSession::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
