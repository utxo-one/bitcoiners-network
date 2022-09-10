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
        
    }

    public function show()
    {
        return view('transaction.deposit.show');
    }
}
