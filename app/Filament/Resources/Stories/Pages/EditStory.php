<?php

namespace App\Filament\Resources\Stories\Pages;

use App\Filament\Resources\Stories\StoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStory extends EditRecord
{
    protected static string $resource = StoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($data['story_type'] === 'image') {
            $data['image_path'] = $data['media_url'];
        } else {
            $data['video_path'] = $data['media_url'];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['story_type'] === 'image') {
            $data['media_url'] = $data['image_path'];
        } else {
            $data['media_url'] = $data['video_path'];
        }

        return $data;
    }
}
