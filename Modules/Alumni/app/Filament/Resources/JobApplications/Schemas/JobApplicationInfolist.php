<?php

namespace Modules\Alumni\Filament\Resources\JobApplications\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class JobApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Application'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('jobPosting.name')
                            ->label(FilamentUi::text('Job posting')),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('user.name')
                            ->label(FilamentUi::text('Applicant'))
                            ->placeholder('-'),
                        TextEntry::make('user.email')
                            ->label(FilamentUi::text('Email'))
                            ->placeholder('-'),
                        TextEntry::make('resume_path')
                            ->label(FilamentUi::text('Resume'))
                            ->copyable()
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('cover_letter')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make(FilamentUi::text('Metadata'))
                    ->collapsed()
                    ->schema([
                        KeyValueEntry::make('meta')->placeholder('-'),
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('updated_at')->dateTime(),
                    ]),
            ]);
    }
}
