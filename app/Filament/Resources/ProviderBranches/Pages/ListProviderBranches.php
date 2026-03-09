<?php

namespace App\Filament\Resources\ProviderBranches\Pages;

use App\Filament\Resources\ProviderBranches\ProviderBranchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProviderBranches extends ListRecords
{
    protected static string $resource = ProviderBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
