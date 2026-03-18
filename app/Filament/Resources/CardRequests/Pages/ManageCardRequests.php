<?php

namespace App\Filament\Resources\CardRequests\Pages;

use App\Filament\Resources\CardRequests\CardRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCardRequests extends ManageRecords
{
    protected static string $resource = CardRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
