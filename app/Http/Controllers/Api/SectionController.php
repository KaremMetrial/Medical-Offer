<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\SectionRepositoryInterface;
use App\Http\Resources\SectionResource;
use Illuminate\Http\JsonResponse;

class SectionController extends BaseController
{
    protected $sectionRepository;

    public function __construct(SectionRepositoryInterface $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }

    public function index(): JsonResponse
    {
        $sections = $this->sectionRepository->all(['*'], ['translations']);
        return $this->successResponse([
            'label' => __('message.sections'),
            'sections' => SectionResource::collection($sections)
        ]);
    }
}
