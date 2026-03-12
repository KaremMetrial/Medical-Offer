<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryRepository->getActiveCategories();
        return $this->successResponse([
            'label' => __('message.categories'),
            'items' => CategoryResource::collection($categories)
        ]);
    }

    public function getParentActiveCategoriesBySectionId($sectionId): JsonResponse
    {
        $categories = $this->categoryRepository->getParentActiveCategoriesBySectionId($sectionId);
        return $this->successResponse([
            'label' => __('message.categories'),
            'items' => CategoryResource::collection($categories)
        ]);
    }

    public function show($id): JsonResponse
    {
        $category = $this->categoryRepository->findOrFail($id, ['*'], ['translations', 'children.translations']);
        return $this->successResponse([
            'label' => __('message.category'),
            'item' => new CategoryResource($category)
        ]);
    }
}
