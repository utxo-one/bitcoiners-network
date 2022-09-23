<?php

namespace App\Http\Controllers\Frontend\Follow;

use App\Enums\FollowRequestStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FollowRequestController extends Controller
{
    public function __construct(private FollowService $followService)
    {
    }

    public function delete(Request $request)
    {
        $request->validate(['twitterIds' => 'required|array']);

        return response()->json(
            auth()->user()->followRequests()->whereIn('follow_id', $request->twitterIds)->where('status', FollowRequestStatus::PENDING)->delete(),
            Response::HTTP_OK,
        );
    }
}