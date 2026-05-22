<?php

namespace Modules\Monitoring\Filament\Resources\WebhookSubscriptions\Schemas;

use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WebhookSubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('url')
                ->label(FilamentUi::field('url'))
                ->required()
                ->url()
                ->maxLength(500)
                ->columnSpanFull(),

            MultiSelect::make('events')
                ->label(FilamentUi::field('events'))
                ->options([
                    'enrollment.created' => 'Enrollment Created',
                    'payment.verified' => 'Payment Verified',
                    'workflow.completed' => 'Workflow Completed',
                    'workflow.step_advanced' => 'Workflow Step Advanced',
                    '*' => 'All Events',
                ])
                ->required()
                ->columnSpanFull(),

            TextInput::make('secret')
                ->label(FilamentUi::field('secret'))
                ->required()
                ->password()
                ->maxLength(255)
                ->default(fn () => bin2hex(random_bytes(24))),

            Toggle::make('is_active')
                ->label(FilamentUi::field('is_active'))
                ->default(true),

            Textarea::make('description')
                ->label(FilamentUi::field('description'))
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }
}
