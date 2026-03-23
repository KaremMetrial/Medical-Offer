<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\VisitResource;
use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends BaseController
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Visit::with(['provider', 'companion'])
            ->where('user_id', $user->id);

        if ($request->has('companion_id')) {
            $query->where('companion_id', $request->companion_id);
        } elseif ($request->boolean('only_companions')) {
            $query->whereNotNull('companion_id');
        } elseif ($request->boolean('only_mine')) {
            $query->whereNull('companion_id');
        }

        $visits = $query->latest('visit_date')->paginate($request->per_page ?? 15);
        
        return $this->successResponse([
            'visit_list' => VisitResource::collection($visits)->response()->getData(true)
        ]);
    }

    public function show($id)
    {
        $visit = Visit::with(['provider', 'companion'])->findOrFail($id);
        
        return $this->successResponse(new VisitResource($visit));
    }
}
