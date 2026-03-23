<?php

namespace App\Filament\Resources\ManualNotifications\Pages;

use App\Filament\Resources\ManualNotifications\ManualNotificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use App\Models\User;
use App\Notifications\GeneralNotification;

use Filament\Support\Enums\Width;

class ManageManualNotifications extends ManageRecords
{
    protected static string $resource = ManualNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                // ->modalWidth(Width::Full)
                ->after(function ($record) {
                    $notification = new GeneralNotification($record->title, $record->message);

                    if ($record->target_type === \App\Enums\ManualNotificationTarget::ALL) {
                        User::all()->each(fn($user) => $user->notify($notification));
                    } elseif ($record->user_id) {
                        User::find($record->user_id)?->notify($notification);
                    }
                }),
        ];
    }
}
