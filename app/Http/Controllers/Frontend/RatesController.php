<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RatesController extends Controller
{
    public function index()
    {
        return [
            'pricing' => config('pricing'),
            'limits' => config('limits'),
        ];
    }
}