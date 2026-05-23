<?php

namespace Modules\EOffice\Filament\Resources\Letters;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EOffice\Filament\Resources\Letters\Pages\CreateLetter;
use Modules\EOffice\Filament\Resources\Letters\Pages\EditLetter;
use Modules\EOffice\Filament\Resources\Letters\Pages\ListLetters;
use Modules\EOffice\Filament\Resources\Letters\Pages\ViewLetter;
use Modules\EOffice\Filament\Resources\Letters\Schemas\LetterForm;
use Modules\EOffice\Filament\Resources\Letters\Tables\LettersTable;
use Modules\EOffice\Models\Letter;

class LetterResource extends ModuleResource
{
    protected static ?string $model = Letter::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LetterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LettersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLetters::route('/'),
            'create' => CreateLetter::route('/create'),
            'view' => ViewLetter::route('/{record}'),
            'edit' => EditLetter::route('/{record}/edit'),
        ];
    }
}
