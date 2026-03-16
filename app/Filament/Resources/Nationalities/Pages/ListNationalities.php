<?php

namespace App\Filament\Resources\Nationalities\Pages;

use App\Filament\Resources\Nationalities\NationalityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNationalities extends ListRecords
{
    protected static string $resource = NationalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
