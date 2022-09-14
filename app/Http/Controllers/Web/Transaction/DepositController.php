<?php

namespace App\Http\Controllers\Web\Transaction;

use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepositRequest;
use App\Services\TransactionService;
use Illuminate\Http\Request;


class DepositController extends Controller
{
    public function __construct(private TransactionService $transactionService)
    {
    }

    public function index()
    {
        return view('transaction.deposit.index');
    }

    public function store(StoreDepositRequest $request)
    {
        $invoice = $this->transactionService->createInvoice($request->amount);

        return redirect($invoice->getData()['checkoutLink']);
    }

    public function show()
    {
        return view('transaction.deposit.show');
    }
}
