<?php

namespace App\Filament\Resources\Nationalities\Pages;

use App\Filament\Resources\Nationalities\NationalityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNationality extends CreateRecord
{
    use \App\Filament\Traits\TranslatesRecordOnCreate;
    protected static string $resource = NationalityResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
