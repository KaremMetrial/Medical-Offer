<?php

namespace App\Filament\Resources\ProviderBranches\Pages;

use App\Filament\Resources\ProviderBranches\ProviderBranchResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProviderBranch extends EditRecord
{
    use \App\Filament\Traits\TranslatesRecordOnEdit;

    protected static string $resource = ProviderBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
