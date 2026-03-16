<?php

namespace App\Filament\Resources\Nationalities\Pages;

use App\Filament\Resources\Nationalities\NationalityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNationality extends EditRecord
{
    use \App\Filament\Traits\TranslatesRecordOnEdit;
    protected static string $resource = NationalityResource::class;

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
