<?php

namespace App\Repositories\Contracts;

interface StoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getStoriesByProviderId($providerId);
    public function getWithActiveStories($user = null, $ip = null);
    public function recordView($storyId, array $data);
}
