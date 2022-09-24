<?php

namespace App\Http\Controllers\Frontend\Transaction;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepositRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DebitController extends Controller
{
    public function __construct(private TransactionService $transactionService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json(
            auth()->user()->transactions()->where('type', TransactionType::DEBIT)->paginate(),
            Response::HTTP_OK
        );
    }
}
