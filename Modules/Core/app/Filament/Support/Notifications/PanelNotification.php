<?php

namespace Modules\Core\Filament\Support\Notifications;

use Filament\Notifications\Notification;
use Modules\Core\Support\FilamentUi;

class PanelNotification
{
    public static function success(string $englishTitle, ?string $englishBody = null): Notification
    {
        $notification = Notification::make()
            ->title(FilamentUi::text($englishTitle))
            ->success();

        if ($englishBody !== null && $englishBody !== '') {
            $notification->body(FilamentUi::text($englishBody));
        }

        return $notification;
    }

    public static function danger(string $englishTitle, ?string $englishBody = null): Notification
    {
        $notification = Notification::make()
            ->title(FilamentUi::text($englishTitle))
            ->danger();

        if ($englishBody !== null && $englishBody !== '') {
            $notification->body($englishBody);
        }

        return $notification;
    }
}
