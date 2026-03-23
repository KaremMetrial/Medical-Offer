<?php

namespace App\Filament\Resources\ManualNotifications;

use App\Filament\Resources\ManualNotifications\Pages\ManageManualNotifications;
use App\Filament\Resources\ManualNotifications\Schemas\ManualNotificationForm;
use App\Filament\Resources\ManualNotifications\Schemas\ManualNotificationInfolist;
use App\Filament\Resources\ManualNotifications\Tables\ManualNotificationsTable;
use App\Models\ManualNotification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ManualNotificationResource extends Resource
{
    protected static ?string $model = ManualNotification::class;
    
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.group.users');
    }

    public static function getModelLabel(): string
    {
        return __('filament.manual_notification.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.manual_notification.plural_label');
    }


    public static function form(Schema $schema): Schema
    {
        return ManualNotificationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ManualNotificationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ManualNotificationsTable::configure($table);
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
            'index' => ManageManualNotifications::route('/'),
        ];
    }

}
