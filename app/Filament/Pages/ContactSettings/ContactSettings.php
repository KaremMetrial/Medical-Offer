<?php

namespace App\Filament\Pages\ContactSettings;

use App\Models\Country;
use App\Models\CountryTranslation;
use App\Filament\Pages\ContactSettings\Schemas\ContactSettingsForm;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use BackedEnum;

class ContactSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    protected string $view = 'filament.pages.contact-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $countries = Country::with('translations')->get();
        
        $countryData = [];
        foreach ($countries as $country) {
            $data = [
                'id' => $country->id,
                'name' => $country->name,
                'contact_email' => $country->contact_email,
                'contact_phone' => $country->contact_phone,
                'contact_whatsapp' => $country->contact_whatsapp,
            ];

            // Handle translatable title
            foreach (config('languages.supported', ['ar' => 'Arabic', 'en' => 'English']) as $code => $name) {
                $data["contact_title:{$code}"] = $country->translations->where('local', $code)->first()?->contact_title;
            }

            $countryData[] = $data;
        }

        $this->form->fill(['countries' => $countryData]);
    }

    public function form(Schema $form): Schema
    {
        return ContactSettingsForm::configure($form)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data['countries'] as $countryData) {
            $country = Country::find($countryData['id']);
            if (!$country) continue;

            $country->update([
                'contact_email' => $countryData['contact_email'],
                'contact_phone' => $countryData['contact_phone'],
                'contact_whatsapp' => $countryData['contact_whatsapp'],
            ]);

            foreach (config('languages.supported', ['ar' => 'Arabic', 'en' => 'English']) as $code => $name) {
                $titleKey = "contact_title:{$code}";
                if (isset($countryData[$titleKey])) {
                    CountryTranslation::updateOrCreate(
                        ['country_id' => $country->id, 'local' => $code],
                        ['contact_title' => $countryData[$titleKey]]
                    );
                }
            }
        }

        Notification::make()
            ->title(__('filament.notifications.saved'))
            ->success()
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.contact_settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.group.settings');
    }
}
