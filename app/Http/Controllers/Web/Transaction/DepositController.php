<?php

namespace App\Http\Controllers\Web\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepositRequest;
use Illuminate\Http\Request;


class DepositController extends Controller
{
    public function index()
    {
        return view('transaction.deposit.index');
    }

    public function store(StoreDepositRequest $request)
    {
        try {
            $client = new Invoice($host, $apiKey);

                $client->createInvoice(
                    $storeId,
                    $currency,
                    PreciseNumber::parseString($amount),
                    $orderId,
                    $buyerEmail
                );

        } catch (\Throwable $e) {
            echo "Error: " . $e->getMessage();
        }

        return redirect()->route('transaction.deposit.index');
    }
}
