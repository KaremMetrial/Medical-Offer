<?php

namespace App\Filament\Resources\ProviderBranches\Pages;

use App\Filament\Resources\ProviderBranches\ProviderBranchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProviderBranch extends CreateRecord
{
    use \App\Filament\Traits\TranslatesRecordOnCreate;

    protected static string $resource = ProviderBranchResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
