<?php

namespace App\Http\Controllers\Frontend\Transaction;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepositRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DepositController extends Controller
{
    public function __construct(private TransactionService $transactionService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json(
            auth()->user()->transactions()->where('type', TransactionType::CREDIT)->paginate(),
            Response::HTTP_OK
        );
    }

    public function store(StoreDepositRequest $request)
    {
        $invoice = $this->transactionService->createInvoice($request->amount, $request->redirectUrl);

        return response()->json($invoice->getData(), Response::HTTP_OK);
    }

    public function show()
    {
        return view('transaction.deposit.show');
    }
}
