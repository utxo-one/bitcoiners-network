<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AvailableBalanceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(auth()->user()->getAvailableBalance(), Response::HTTP_OK);
    }
}