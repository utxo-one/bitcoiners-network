<?php

namespace App\Http\Controllers\Frontend\Follow;

use App\Enums\FollowRequestStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMassFollowRequest;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MassFollowController extends Controller
{
    public function __construct(private FollowService $followService)
    {
    }

    public function index()
    {
        return response()->json(
            $this->followService->getMassFollowSummary(), Response::HTTP_OK
        );
    }

    public function delete()
    {
        return response()->json(
            auth()->user()->followRequests()->where('status', FollowRequestStatus::PENDING)->delete(),
            Response::HTTP_OK,
        );
    }

    public function store(StoreMassFollowRequest $request)
    {
        try {
            return response()->json($this->followService->createFollowRequests(
                user: auth()->user(),
                amount: $request->amount,
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

    }
}
