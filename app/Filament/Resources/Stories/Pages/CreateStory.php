<?php

namespace App\Filament\Resources\Stories\Pages;

use App\Filament\Resources\Stories\StoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStory extends CreateRecord
{
    protected static string $resource = StoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['story_type'] === 'image') {
            $data['media_url'] = $data['image_path'] ?? null;
        } else {
            $data['media_url'] = $data['video_path'] ?? null;
        }

        return $data;
    }
}
