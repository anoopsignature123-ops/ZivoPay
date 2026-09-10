<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ArbitrageController extends Controller
{
    /**
     * Display Live Arbitrage Trading Dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();

        return view('user.arbitrage', compact('user'));
    }
}
