<?php

namespace App\Filament\Resources\MemberPlans\Pages;

use App\Filament\Resources\MemberPlans\MemberPlanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMemberPlan extends CreateRecord
{
    use \App\Filament\Traits\TranslatesRecordOnCreate;

    protected static string $resource = MemberPlanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
