<?php

namespace App\Http\Controllers\Frontend\Follow\Scopes;

use App\Enums\FollowRequestStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FollowRequestScopeController extends Controller
{
    public function __construct(private FollowService $followService)
    {
    }

    public function completed()
    {
        return response()->json(
            auth()->user()->followRequests()
                ->with('follow')
                ->where('status', FollowRequestStatus::COMPLETED)
                ->orderBy('created_at', 'desc')
                ->paginate(),
            Response::HTTP_OK
        );
    }

    public function pending()
    {
        return response()->json(
            auth()->user()->followRequests()
                ->where('status', FollowRequestStatus::PENDING)
                ->with(['follow' => function ($query) {
                    $query->where('type', UserType::BITCOINER);
                }])->paginate(),
            Response::HTTP_OK
        );
    }
}
