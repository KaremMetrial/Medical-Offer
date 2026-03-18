<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Http\Resources\CategoryResource;
use App\Filters\CategoryFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $filter = new CategoryFilter($request);
        $categories = $this->categoryRepository->getActiveCategories($filter);
        return $this->successResponse([
            'label' => __('message.categories'),
            'categories' => CategoryResource::collection($categories)
        ]);
    }

    public function getParentActiveCategoriesBySectionId(Request $request, $sectionId): JsonResponse
    {
        $filter = new CategoryFilter($request);
        $categories = $this->categoryRepository->getParentActiveCategoriesBySectionId($sectionId, $filter);
        return $this->successResponse([
            'label' => __('message.categories'),
            'categories' => CategoryResource::collection($categories)
        ]);
    }

    public function show($id): JsonResponse
    {
        $category = $this->categoryRepository->findOrFail($id, ['*'], ['translations', 'children.translations']);
        return $this->successResponse([
            'label' => __('message.category'),
            'category' => new CategoryResource($category)
        ]);
    }
}
