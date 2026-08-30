<?php

namespace Modules\Alumni\Filament\Resources\JobApplications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Alumni\Models\JobApplication;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class JobApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Section::make(FilamentUi::text('Applicant'))
                    ->columns(2)
                    ->schema([
                        Select::make('job_posting_id')
                            ->relationship('jobPosting', 'name')
                            ->disabled(),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled(),
                        TextInput::make('resume_path')
                            ->disabled()
                            ->columnSpanFull(),
                        Textarea::make('cover_letter')
                            ->disabled()
                            ->rows(8)
                            ->columnSpanFull(),
                    ]),
                Section::make(FilamentUi::text('Review'))
                    ->schema([
                        Select::make('status')
                            ->options(fn (?JobApplication $record): array => $record?->reviewStatusOptions() ?? JobApplication::statusOptions())
                            ->required(),
                    ]),
            ]);
    }
}
