<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends BaseController
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        Complaint::create([
            'user_id' => $request->user()?->id,
            'phone' => $request->phone,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return $this->successResponse(null, __('message.complaint_submitted_successfully'));
    }
}
